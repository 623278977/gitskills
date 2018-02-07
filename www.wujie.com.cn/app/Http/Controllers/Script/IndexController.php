<?php

namespace App\Http\Controllers\Script;

use App\Http\Controllers\CommonController;
use App\Models\ActivityApply;

use App\Models\Activity;

use App\Models\ActivityProjectVideo;


use App\Models\ProjectMember;

use App\Models\ProjectPhoto;

use App\Models\UserProject;

use App\Models\UserInfo;
use App\Models\Project;
use App\models\User;
use App\models\RoleUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \DB;

class IndexController extends CommonController
{

    /**
     * 用户信息▒|(
     *
     * @return Response
     */
    public function getUserinfo()
    {
        $users = User::all();
        foreach ($users as $val) {
            $info = UserInfo::where("uid", $val->uid)->first();
            if (empty($info->uid)) {
                UserInfo::insert(array('uid' => $val->uid));
            }
        }

    }

    /**
     *
     *    Activity             |要处▒~F
     * | Activityphoto        |
     * | Activityprojectvideo |要处▒~F
     * | Ad                   |
     * | AdBackground         |
     * | Adposition           |
     * | Article              |
     * | Institution          |要处▒~F
     * | Project              |要处▒~F
     * | ProjectJob           |
     * | Projectmember        |要处▒~F
     * | ProjectPhoto         |要处▒~F
     * | Throughtrain2        |
     * | UserCompany          |
     * | UserInfo             |要处▒~F
     * | UserItem             |
     * | UserProject          |要处▒~F
     * | Video
     */
    public function getImage(Request $request)
    {
        $images = DB::table('image')->get();
        $type = $request->type;
        foreach ($images as $val) {
            if ($val->url) {
                $url = $this->chuli($val->url);
                if ($val->model == "Activity" && $type == "Activity") {
                    if ($val->field == "bg_img") {
                        Activity::where('id', $val->post_id)->update(array('bg_img' => $url));
                    } elseif ($val->field == "haibao") {
                        Activity::where('id', $val->post_id)->update(array('haibao' => $url));
                    } elseif ($val->field == "photo") {
                        Activity::where('id', $val->post_id)->update(array('photo' => $url));
                    } elseif ($val->field == "wx_photo") {
                        Activity::where('id', $val->post_id)->update(array('wx_photo' => $url));
                    }
                } elseif ($val->model == "Activityprojectvideo" && $type == "Activityprojectvideo") {
                    ActivityProjectVideo::where('id', $val->post_id)->update(array('image' => $url));
                } elseif ($val->model == "Institution" && $type == "Institution") {
                    if ($val->field == "icon") {
                        DB::table('institution')->where('id', $val->post_id)->update(array('icon' => $url));
                    } elseif ($val->field == "license") {
                        DB::table('institution')->where('id', $val->post_id)->update(array('license' => $url));
                    }
                } elseif ($val->model == "Project" && $type == "Project") {
                    Project::where('id', $val->post_id)->update(array('photo' => $url));
                } elseif ($val->model == "Projectmember" && $type == "Projectmember") {
                    ProjectMember::where('id', $val->post_id)->update(array('photo' => $url));
                } elseif ($val->model == "ProjectPhoto" && $type == "ProjectPhoto") {
                    ProjectPhoto::where('id', $val->post_id)->update(array('image' => $url));
                } elseif ($val->model == "UserInfo" && $type == "UserInfo") {
                    if ($val->field == "avatar") {
                        User::where("uid", $val->post_id)->update(array('avatar' => $url));
                    } elseif ($val->field == "business_card") {
                        UserInfo::where("uid", $val->post_id)->update(array('business_card' => $url));
                    }
                } elseif ($val->model == "UserProject" && $type == "UserProject") {
                    UserProject::where('id', $val->post_id)->update(array('license' => $url));
                }
            }
        }
    }

    function getDealactivityapplyerweima()
    {
        $activityApplys = ActivityApply::all();
        foreach ($activityApplys as $k => $v) {
            $file_name = unique_id() . '.png';
            $qrcode_path = img_create('apply_id:' . $v->id, $file_name, $this->uploadDir);
            ActivityApply::where('id', $v->id)->update(array('qrcode' => $qrcode_path));
        }
        return AjaxCallbackMessage('ok', true);
    }

    function chuli($image)
    {
        return str_replace("./", "", $image);
    }

    function getT()
    {
        echo $this->chuli('./attached/image/20150729/15072916052951bd31b713b2b7.jpg');
    }

    function getClear()
    {
        opcache_reset();
    }

    function getWzd()
    {
        //刷新完整▒|&
        $Projects = Project::all();
        foreach ($Projects as $val) {
            Project::refreshProgress($val->id);
        }
    }
}
