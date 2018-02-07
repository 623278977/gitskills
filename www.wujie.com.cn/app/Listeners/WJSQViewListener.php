<?php

namespace App\Listeners;
use Illuminate\Session\Store;
use App\Events\WJSQView;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


class WJSQViewListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Store $session)
    {
        $this->session = $session;
    }

    /**
     * Handle the event.
     *
     * @param  WJSQView  $event
     * @return void
     */
    public function handle(WJSQView $event)
    {
        $post = $event->post;
        $model=$event->model;
        //先进行判断是否已经查看过
        if (!$this->hasViewed($model,$post)) {
            //保存到数据库
            $post->view = $post->view + 1;
            $post->save();
            //看过之后将保存到 Session
            $this->storeViewed($model,$post);
        }
    }
    protected function hasViewed($model,$post)
    {
        return array_key_exists($model.'_'.$post->id, $this->getViewed());
    }

    protected function getViewed()
    {
        return $this->session->get('wjsqView', []);
    }

    protected function storeViewed($model,$post)
    {
        $key = 'wjsqView.'.$model.'_'.$post->id;

        $this->session->put($key,'aaa');

    }
}
