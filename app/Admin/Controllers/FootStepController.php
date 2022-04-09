<?php

namespace App\Admin\Controllers;

use App\FootStep;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class FootStepController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'FootStep';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new FootStep());

        $grid->column('id', __('Id'));
        $grid->column('footnum', __('Footstep'));
        $grid->column('position', __('Position'));
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
        $show = new Show(FootStep::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('footnum', __('Footstep'));
        $show->field('position', __('Position'));
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
        $form = new Form(new FootStep());

        $form->number('footnum', __('Footstep'));
        $form->number('position', __('Position'));

        return $form;
    }
}
