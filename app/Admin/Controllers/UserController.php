<?php

namespace App\Admin\Controllers;

use App\User;
use App\Group;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UserController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'User';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('gid', __('群組'))->display(function($gid){
            $gName = Group::where('id', $gid)->first()->name;
            return $gName;
        });
        $grid->column('email', __('Email'));
        //$grid->column('password', __('Password'));
        //$grid->column('remember_token', __('Remember token'));
        //$grid->column('created_at', __('Created at'));
        //$grid->column('updated_at', __('Updated at'));
        $grid->column('mobile', __('Mobile'));
        $grid->column('line_token', __('Line token'));

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
        $show = new Show(User::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('email', __('Email'));
        $show->field('password', __('Password'));
        $show->field('remember_token', __('Remember token'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('mobile', __('Mobile'));
        $show->field('line_token', __('Line token'));

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
        
        $form = new Form(new User());

        $form->text('name', '姓名');
        $form->email('email', __('Email'));
        $form->password('password', __('Password'));
        $form->text('mobile', '電話')->options(['mask' => '9999999999']);
        $form->text('line_token', __('Line Token'));
        $form->select('gid', __('群組'))->options($groupSet);

        $form->saving(function (Form $form) {
            if ($form->password == null)
            {
                $form->password = $form->model()->password;
            }
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = bcrypt($form->password);
            }
        });

        return $form;
    }
}
