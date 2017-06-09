<?php namespace Jab\Comments\Models;

use App;
use Auth;
use Str;
use Lang;
use Model;
use Markdown;
use Backend\Models\User;

class Comment extends Model
{
    public $table = 'jab_comments_comments';

    /**
     * Relations
     */
    public $belongsTo = [
        'user' => ['RainLab\User\Models\User']
    ];

    /**
     * The attributes that should be mutated to dates.
     * @var array
     */
    protected $dates = ['published_at'];

    public function scopeIsPublished($query)
    {
        return $query
            ->whereNotNull('published')
            ->where('published', '=', 1);
    }

    /**
     * @param $params
     * @return bool|static
     *
     * Create comment
     *
     */
    public static function createComment(&$params) {
        if(is_array($params) && !empty($params)) {
            $comment = new static;
            foreach($params as $key => $val) {
                $comment->$key = $val;
            }
            $comment->published_at = date('Y-m-d H:i:s');;
            $result = $comment->save();

            if($result) {
                return $comment;
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }

    /**
     * @param $id
     * @param $params = array($content, $content_html)
     * @param $user
     * @return bool
     *
     * Update comment
     *
     */
    public static function updateComment($id, &$params, $user) {
        if(is_numeric($id) && !empty($params)) {
            $comment = self::find($id);
            if($user->id == $comment->user_id && $user->id != 0) {
                $content = $params['content'];
                $content_html = $params['content_html'];

                $comment->content = $content;
                $comment->content_html = $content_html;

                $result = $comment->save();

                $comment->user_avatar = $user->getAvatarThumb(100);
                $comment->user_name = $user->name;


                if($result) {
                    return $comment;
                }
                else {
                    return false;
                }
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }
}