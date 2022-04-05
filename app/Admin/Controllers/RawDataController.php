<?php

namespace App\Admin\Controllers;

use App\RawData;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\User;

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
        $grid->column('name', __('Name'));
        $grid->column('phone', __('Phone'));
        $grid->column('status', __('Status'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

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
        $form = new Form(new RawData());

        $form->text('name', __('姓名'));
        $form->mobile('phone', __('電話'))->options(['mask' => '9999999999']);
        $form->select('status', __('狀態'))->options([
            'KEEP' => '保留',
            'PASS' => '發送',
        ]);

        $form->saving(function (Form $form) {
            $users = User::all();
            if ($form->status == 'PASS')
            {
                foreach ($users as $_user) {
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

                }
            }
        });

        return $form;
    }
}
