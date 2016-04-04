<?php
namespace Home\Controller;
use Think\Controller;
class PhotoController extends Controller {


    public function index(){

    }

    public function search(){
        $stunum = I('post.stunum');        
        $photo = M('photo');
        $condition = array(
                "stunum" => I('post.stunum')
            );
        $goal = $photo->where($condition)->field('stunum,date,photosrc')->find();
        if($goal){
             $info = array(
                    'state' => 200,
                    'info'  => 'success',
                    'data'  => $goal,
                );
         }else{
             $info = array(
                    'state' => 404,
                    'info'  => 'failed',
                    'data'  => array(),
                );
         }
         echo json_encode($info,true);
    }

    public function upload(){
        $photo = M('photo');
        $condition = array(
                "stunum" => I('post.stunum')
            );
        if(I('post.stunum') == null){
            $info = array(
                    'state' => 404,
                    'info'  => 'failed',
                    'data'  => array(),
                );
            echo json_encode($info,true);
            exit;
        }
        $checkExist = $photo->where($condition)->find();
        $upload = new \Think\Upload();
        $upload->maxSize = 512000;
        $upload->exts = array('png', 'jpeg',"jpg" , 'PNG','JPEG','JPG');
        $upload->rootPath  =  "./Public/photo/";
        $upload->saveName = time().'_'.mt_rand();
        $upload->autoSub = false;
        $a = $upload->upload();
        if($upload->getError() != null){
            $info = array(
                'state' => 404,
                'info'  => 'failed',
                'data'  => array(),
            );
        }else{
            $site = $_SERVER["SERVER_NAME"];
            $folder_name = explode('/',$_SERVER["SCRIPT_NAME"]);
            $thunmbnail_src =  "http://".$site.'/'.$folder_name[1].'/Public/photo/thumbnail/'.$upload->saveName.".".$a['fold']['ext'];
            $content = array(
                "stunum"   => I('post.stunum'),
                "date"     => date("Y-m-d H:i:s", time()),
                "photosrc" => "http://".$site.'/'.$folder_name[1]."/Public/photo/".$upload->saveName.".".$a['fold']['ext'],
                "photo_thumbnail_src" => $thunmbnail_src,
                'state'    => 1,
            );
            $thumbnail = new \Think\Image();
            $thumbnail->open('./Public/photo/'.$upload->saveName.".".$a['fold']['ext']);
            $thumbnail->thumb(150, 150)->save($thunmbnail_src);
            if($checkExist != NULL){
                $goal = $photo->where($condition)->data($content)->save();
            }else{
                $goal = $photo->add($content);
            }
            if($goal){
                $info = array(
                    'state' => 200,
                    'info'  => 'success',
                );
            }else{
                $info = array(
                    'state' => 404,
                    'info'  => 'failed',
                    'data'  => array(),
                );
            }
        }
        echo json_encode($info,true);
    }

    public function _empty() {
        $this->display('Empty/index');
    }
}
