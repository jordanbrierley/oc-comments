<?php namespace Jab\Comments\Components;

use DB;
use Log;
use Str;
use Auth;
use Input;
use Markdown;
use BackendAuth;
use Cms\Classes\ComponentBase;
use Jab\Comments\Models\Comment;
use Jab\Comments\Classes\Helper;
use Jab\Comments\Models\Settings;

class Comments extends ComponentBase
{

    /**
     * A collection of comments to display.
     * @var Collection
     */
    public $comments;

    /**
     * Reference to the user.
     * @var Model
     */
    public $user;

    /**
     * Parameter to use for the comment reply depth.
     * @var string
     */
    public $depth;

    public function componentDetails()
    {
        return [
            'name' => 'jab.comments::lang.comments.comments',
            'description' => 'jab.comments::lang.comments.comments_description'
        ];
    }

    public function defineProperties()
    {
        return [
            'slug' => [
                'title' => 'jab.comments::lang.settings.slug',
                'description' => 'jab.comments::lang.settings.slug_description',
                'default' => '{{ :slug }}',
                'type' => 'string',
                'required' => true,
            ],
            'depth' => [
                'title' => 'jab.comments::lang.settings.depth',
                'description' => 'jab.comments::lang.settings.depth_description',
                'default' => '0',
                'type' => 'string',
                'required' => true,
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'jab.comments::lang.settings.depth_validation_message'
            ],
        ];
    }

    /**
     * Executed when this component is bound to a page or layout.
     */
    public function onRun()
    {
        // add some assets
        if(Settings::get('css', true)) {
            $this->addCss('assets/css/comments.css');
        }
        $this->addJs('assets/js/comments.js');

        $this->prepareVars();

        // add comments to template
        $this->comments = $this->page['comments'] = $this->loadComments();
    }

    protected function prepareVars()
    {
        // get current user
        $user = self::getUser();
        // check if user exist
        if ($user) {
            $this->page['loggedin'] = true;
        }
        else {
            $this->page['loggedin'] = false;
        }
        // add current user to template
        $this->user = $this->page['user'] = $user;
        // add comment reply depth to template
        $this->depth = $this->page['depth'] = $this->property('depth');

        $dataOptions = '';

        if(Settings::get('hide_reply_form', true)) {
            $dataOptions .= 'hideReplyForm: 1, ';
        }
        else {
            $dataOptions .= 'hideReplyForm: 0, ';
        }

        if(Settings::get('hide_main_form', true)) {
            $dataOptions .= 'hideMainForm: 1';
        }
        else {
            $dataOptions .= 'hideMainForm: 0';
        }

        $this->page['dataOptions'] = $dataOptions;
    }

    public function onComment()
    {
        // add comment reply depth to template
        $this->depth = $this->page['depth'] = $this->property('depth');
        $this->page['level'] = post('level');

        // get current user
        $user = self::getUser();
        if($user) {
            $user_id = $user->id;
        }
        else {
            $user_id = 0; // guest
        }

        // get id of edited comment
        $id = post('id');
        if(!$id) { // create new comment

            // get slug
            $cid = $this->property('slug');
            $params['cid'] = $cid;
            // get parent id
            $params['pid'] = post('pid');
            // get content
            $params['content'] = post('content');
            $params['user_id'] = $user_id;
            // get ip address
            $params['hostname'] = self::getHostname();
            // parse content to html
            $params['content_html'] = Markdown::parse(strip_tags(trim($params['content'])));
            $params['published_at'] = date('Y-m-d H:i:s');

            // get name, mail, homepage if user is anonymous
            $params['name'] = post('name');
            $params['mail'] = post('mail');
            $params['homepage'] = post('homepage');

            // create comment
            $comment = Comment::createComment($params);

            // add user name and avatar to comment
            if($user_id != 0) {
                $comment->user_avatar = $user->getAvatarThumb(64);
                $comment->user_name = $user->name;
            }

            // if success return comment
            if ($comment) {
                $this->comment = $this->page['comment'] = $comment;
            }
            else {
                $this->page['message'] = e(trans('jab.comments::lang.messages.error'));
            }
        }
        else { // update comment

            // get content
            $content = post('content');
            $params = array(
                'content' => post('content'),
                'content_html' => Markdown::parse(trim($content)),
            );
            // update content
            $comment = Comment::updateComment($id, $params, $user);

            // if success return comment
            if($comment) {
                $this->comment = $this->page['comment'] = $comment;
            }
            else {
                $this->page['message'] = e(trans('jab.comments::lang.messages.edit_error'));
            }
        }
    }

    public function onEditComment()
    {
        // get comment id
        $id = post('id');
        // get user
        $user = self::getUser();
        // find comment
        $comment = Comment::find($id);

        // if user is author return comment content
        if(isset($user) && $user->id == $comment->user_id && $user->id != 0) {
            $res = array(
                'id' => $id,
                'content' => $comment->content
            );
            return json_encode($res);
        }
        else {
            return;
        }

    }

    public function onDeleteComment()
    {
        // get comment id
        $id = post('id');
        // get user
        $user = self::getUser();
        // find comment
        $comment = Comment::find($id);

        // if user is author delete comment
        if(isset($user) && $user->id == $comment->user_id && $user->id != 0) {
            $comment->delete();
            $helper = new Helper;
            $comments = $helper->getChildren($id);
            $comments[] = $id;
            DB::table('jab_comments_comments')
                ->whereIn('id', $comments)
                ->delete();
            return $id;
        }
        else {
            return;
        }

    }

    /**
     * @param bool $uid
     * @return bool
     *
     * Get User info.
     *
     */
    public function getUser($uid = false) {
        if($uid !== false) {
            $user = Auth::findUserById($uid);
        }
        else if($uid === false) {
            $user = Auth::getUser();
        }
        else {
            return false;
        }

        if(!isset($user)) {
            return false;
        }
        else {
            $user->id = $user['attributes']['id'];
            $user->name = $user['attributes']['name'];
            return $user;
        }
    }

    /**
     * @return mixed
     *
     * Get IP
     *
     */
    private function getHostname() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $hostname = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $hostname = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else {
            $hostname = $_SERVER['REMOTE_ADDR'];
        }

        return $hostname;
    }

    /**
     * @return mixed
     *
     * Get all comments
     *
     */
    protected function loadComments()
    {
        $cid = $this->property('slug');
        $comments = Comment::isPublished()->where('cid', $cid)->get();

        foreach($comments as $key => $comment) {
            // get user
            $comment_author = self::getUser($comment->user_id);
            if($comment_author) {
                // get user avatar
                $comments[$key]->user_avatar = $comment_author->getAvatarThumb(64);

                // Check if user name exist
                if(isset($comment_author->name))
                    $comments[$key]->user_name = $comment_author->name;
                else
                    $comments[$key]->user_name = '';
            }
        }

        return $comments;
    }
}
