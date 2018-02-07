<?php

namespace App\Services;

use App\Models\Comment\Entity as Comment;
use App\Models\Brand\Images;
use App\Models\Brand\Quiz;
use App\Models\News\Entity as News;
use App\Models\User\Favorite;
use App\Models\Ad;
class CommentService
{
    public function getSingleComment($id)
    {
        $comment = \DB::table('comment')
            ->leftJoin('user', 'comment.uid', '=', 'user.uid')
            ->where('id', $id)->select('comment.content', 'user.avatar', 'user.nickname', 'comment.nickname as comment_nickname')->first();
        $comment->avatar = getImage($comment->avatar, 'avatar', '', 0);
        if(!$comment->uid){
            $comment->nickname = $comment->comment_nickname;
        }
        $images =Comment::getImages($id);

        return compact('comment', 'images');
    }


}