<?php

namespace App\Admin\Controllers;

use App\RawData;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\User;
use App\FootStep;

class RawDataController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'RawData';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new RawData());

        $grid->column('id', __('Id'));
        $grid->column('name', __('姓名'));
        $grid->column('phone', __('電話'));
        $grid->column('gid', __('群組'))->display(function($gid){
            try {
                $gName = Group::where('id', $gid)->first()->name;
                return $gName;
            } catch (\Throwable $th) {
                return '無群組';
            }
        });
        $grid->column('status', __('狀態'));
        $grid->column('created_at', __('建立時間'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(RawData::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('phone', __('Phone'));
        $show->field('status', __('Status'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {

        $groupSet = array();
        $groups = Group::All();

        foreach ($groups as $item) {
            $groupSet[$item->id] = $item->name;
        }

        $form = new Form(new RawData());

        $form->text('name', __('姓名'));
        $form->mobile('phone', __('電話'))->options(['mask' => '9999999999']);
        $form->select('gid', __('群組'))->options($groupSet);
        $form->select('status', __('狀態'))->options([
            'KEEP' => '保留',
            'PASS' => '發送',
        ]);

        $form->saving(function (Form $form) {

            if ($form->status == 'PASS')
            {
                $users = User::all();
                $fooStep = FootStep::orderBy('created_at', 'desc')->first();
                $pointer = $fooStep->position;
                $footNum = $fooStep->footnum;
    
                $memberList = array();
                $memListLen = sizeof($users);
    
                foreach ($users as $_user) { 
                    array_push($memberList, $_user->id);
                }

                for ($i=0; $i < $footNum; $i++) { 
                    $pointer = $pointer%$memListLen;
                    $memId = $memberList[$pointer];

                    $_user = User::where('id', $memId)->first();

                    $access_token = $_user->line_token;
                    $mymessage = '姓名：'.$form->name.' 電話：'.$form->phone;

                    $headers = array(
                        'Content-Type: multipart/form-data',
                        'Authorization: Bearer '.$access_token.''
                    );
                    $message = array(
                        'message' => $mymessage
                    );
                    $ch = curl_init();
                    curl_setopt($ch , CURLOPT_URL , "https://notify-api.line.me/api/notify");
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
                    $result = curl_exec($ch);
                    curl_close($ch);

                    $pointer++;
                }

                $fooStep->position = $pointer;
                $fooStep->save();

                // foreach ($users as $_user) {
                //     $access_token = $_user->line_token;
                //     $mymessage = '姓名：'.$form->name.' 電話：'.$form->phone;

                //     $headers = array(
                //         'Content-Type: multipart/form-data',
                //         'Authorization: Bearer '.$access_token.''
                //     );
                //     $message = array(
                //         'message' => $mymessage
                //     );
                //     $ch = curl_init();
                //     curl_setopt($ch , CURLOPT_URL , "https://notify-api.line.me/api/notify");
                //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                //     curl_setopt($ch, CURLOPT_POST, true);
                //     curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
                //     $result = curl_exec($ch);
                //     curl_close($ch);

                // }
            }
        });

        return $form;
    }
}
