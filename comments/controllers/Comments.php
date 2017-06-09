<?php namespace Jab\Comments\Controllers;

use DB;
use Log;
use Flash;
use Backend;
use BackendMenu;
use Backend\Classes\Controller;
use Jab\Comments\Models\Comment;
use Jab\Comments\Classes\Helper;

class Comments extends Controller
{
    public $implement = [
        'Backend.Behaviors.ListController'
    ];

    public $listConfig = 'config_list.yaml';

    public $bodyClass = 'compact-container';

    public $requiredPermissions = ['jab.comments.access_other_comments'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Xeor.Comments', 'comments', 'comments');
    }

    public function index()
    {
        $this->vars['total'] = Comment::count();
        $this->vars['published'] = Comment::isPublished()->count();
        $this->vars['hidden'] = $this->vars['total'] - $this->vars['published'];
        $this->asExtension('ListController')->index();
    }

    public function index_onDelete()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $id) {
                if (!$comment = Comment::find($id)) {
                    continue;
                }
                $comment->delete();
                $helper = new Helper;
                $comments = $helper->getChildren($id);
                DB::table('jab_comments_comments')
                    ->whereIn('id', $comments)
                    ->delete();
            }

            Flash::success('Successfully deleted those comments.');
        }

        return $this->listRefresh();
    }

    public function index_onHide()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $id) {
                if (!$comment = Comment::find($id)) {
                    continue;
                }
                $comment->published = 0;
                $comment->save();
                $helper = new Helper;
                $comments = $helper->getChildren($id);
                DB::table('jab_comments_comments')
                    ->whereIn('id', $comments)
                    ->update(array('published' => 0));
            }

            Flash::success('Successfully hide those comments.');
        }

        return $this->listRefresh();
    }

    public function index_onShow()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $id) {
                if (!$comment = Comment::find($id)) {
                    continue;
                }
                $comment->published = 1;
                $comment->save();
                $helper = new Helper;
                $comments = $helper->getChildren($id);
                DB::table('jab_comments_comments')
                    ->whereIn('id', $comments)
                    ->update(array('published' => 1));
            }

            Flash::success('Successfully show those comments.');
        }

        return $this->listRefresh();
    }



}