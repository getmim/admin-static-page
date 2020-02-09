<?php
/**
 * PageController
 * @package admin-static-page
 * @version 0.0.1
 */

namespace AdminStaticPage\Controller;

use LibFormatter\Library\Formatter;
use LibForm\Library\Form;
use LibForm\Library\Combiner;
use LibPagination\Library\Paginator;
use StaticPage\Model\StaticPage as SPage;

class PageController extends \Admin\Controller
{
    private function getParams(string $title): array{
        return [
            '_meta' => [
                'title' => $title,
                'menus' => ['static-page']
            ],
            'subtitle' => $title,
            'pages' => null
        ];
    }

    public function editAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_static_page)
            return $this->show404();

        $spage = (object)[];

        $id = $this->req->param->id;
        if($id){
            $spage = SPage::getOne(['id'=>$id]);
            if(!$spage)
                return $this->show404();
            $params = $this->getParams('Edit Static Page');
        }else{
            $params = $this->getParams('Create New Static Page');
        }

        $form              = new Form('admin.static-page.edit');
        $params['form']    = $form;

        $c_opts = [
            'meta' => [null, null, 'json']
        ];

        $combiner = new Combiner($id, $c_opts, 'static-page');
        $spage = $combiner->prepare($spage);

        if(!($valid = $form->validate($spage)) || !$form->csrfTest('noob'))
            return $this->resp('static-page/edit', $params);

        $valid = $combiner->finalize($valid);

        if($id){
            if(!SPage::set((array)$valid, ['id'=>$id]))
                deb(SPage::lastError());
        }else{
            $valid->user = $this->user->id;
            if(!SPage::create((array)$valid))
                deb(SPage::lastError());
        }

        // add the log
        $this->addLog([
            'user'   => $this->user->id,
            'object' => $id,
            'parent' => 0,
            'method' => $id ? 2 : 1,
            'type'   => 'static-page',
            'original' => $spage,
            'changes'  => $valid
        ]);

        $next = $this->router->to('adminStaticPage');
        $this->res->redirect($next);
    }

    public function indexAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_static_page)
            return $this->show404();

        $cond = $pcond = [];
        if($q = $this->req->getQuery('q'))
            $pcond['q'] = $cond['q'] = $q;

        list($page, $rpp) = $this->req->getPager(25, 50);

        $spages = SPage::get($cond, $rpp, $page, ['title'=>true]) ?? [];
        if($spages)
            $spages = Formatter::formatMany('static-page', $spages, ['user']);

        $params             = $this->getParams('Static Page');
        $params['spages']   = $spages;
        $params['form']     = new Form('admin.static-page.index');

        $params['form']->validate( (object)$this->req->get() );

        // pagination
        $params['total'] = $total = SPage::count($cond);
        if($total > $rpp){
            $params['pages'] = new Paginator(
                $this->router->to('adminStaticPage'),
                $total,
                $page,
                $rpp,
                10,
                $pcond
            );
        }

        $this->resp('static-page/index', $params);
    }

    public function removeAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_static_page)
            return $this->show404();

        $id     = $this->req->param->id;
        $spage  = SPage::getOne(['id'=>$id]);
        $next   = $this->router->to('adminStaticPage');
        $form   = new Form('admin.static-page.index');

        if(!$form->csrfTest('noob'))
            return $this->res->redirect($next);

        // add the log
        $this->addLog([
            'user'   => $this->user->id,
            'object' => $id,
            'parent' => 0,
            'method' => 3,
            'type'   => 'static-page',
            'original' => $spage,
            'changes'  => null
        ]);

        SPage::remove(['id'=>$id]);

        $this->res->redirect($next);
    }
}