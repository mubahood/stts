<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\SubGrower\BatchReplicate;
use App\Models\Crop;
use App\Models\CropVariety;
use App\Models\SubGrower;
use App\Models\Utils;
use Carbon\Carbon;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;


class SubGrowerController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Planting Return - Growers';

    /**
     * 
     * 
     

     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SubGrower());

        if (Admin::user()->isRole('admin')) {
            $grid->batchActions(function ($batch) {
                $batch->add(new BatchReplicate());
            });
            $grid->disableCreateButton();
        }

        if (Admin::user()->isRole('inspector')) {
            $grid->disableCreateButton();
        }



        $grid->filter(function ($filter) {
            $filter->equal('status', "Filter by Status")->select([
                '1' => 'Pending',
                '2' => 'Inspection assigned',
                '3' => 'Halted',
                '4' => 'Rejected',
                '5' => 'Accepted',
            ]);
            $filter->equal('crop', "Filter by crop crop")->select(Crop::all()->pluck('name', 'name'));
            $filter->equal('variety', "Filter by crop variety")->select(CropVariety::all()->pluck('name', 'name'));
            $filter->like('district', 'District');
            $filter->like('subcourty', 'Subcouty');
        });




        /*
        
                    return '<span class="badge badge-info">Pending</span>';
        if ($status == 1)
            return '<span class="badge badge-info"></span>';
        if ($status == 2)
            return '<span class="badge badge-primary"></span>';
        if ($status == 3)
            return '<span class="badge badge-warning"></span>';
        if ($status == 4)
            return '<span class="badge badge-danger"></span>';
        if ($status == 5)
            return '<span class="badge badge-success"></span>';
        if ($status == 6)
            return '<span class="badge badge-danger"></span>';
        if ($status == 7)
            return '<span class="badge badge-warning">Provisional</span>';
        if ($status == 8)


        
        for ($i = 0; $i < 300; $i++) {
            # code...

            $sub_g = new SubGrower();
            $faker = \Faker\Factory::create();
            $sub_g->administrator_id = 3;
            $sub_g->name = $faker->name();
            $sub_g->size = $faker->numberBetween(3, 50);
            $sub_g->quantity_planted = $faker->numberBetween(100, 1000);
            $sub_g->expected_yield = $faker->numberBetween(100, 1000);
            $sub_g->phone_number = "0782" . $faker->numberBetween(1000000, 10000000);
            $sub_g->gps_latitude = "0" . $faker->numberBetween(10000, 100000);
            $sub_g->detail = $faker->sentence(100);
            $crops = ['Bush Beans', 'Climbing Beans', 'Ground Nuts,Maize (OPV)'];
            $varieties = ['NABE1', 'NABE2', 'NABE15', 'NABE16', 'NABE17',];
            $districts = ['Kasese', 'Kampala', 'Jinja', 'Mbale', 'Mbarara',];
            shuffle($crops);
            shuffle($varieties);
            shuffle($districts);
            $sub_g->crop = $crops[0];
            $sub_g->variety = $varieties[0];
            $sub_g->district = $districts[0];
            $sub_g->subcourty = $districts[0];
            $sub_g->planting_date = Carbon::now();
            $sub_g->save();
        }*/




        if (Admin::user()->isRole('basic-user')) {
            $grid->model()->where('administrator_id', '=', Admin::user()->id);
            $grid->actions(function ($actions) {
                $status = ((int)(($actions->row['status'])));
                if ($status == 4) {
                    $actions->disableDelete();
                    $actions->disableEdit();
                }
                if (
                    $status != 1
                ) {
                    $actions->disableDelete();
                    $actions->disableEdit();
                }
            });
        } else if (Admin::user()->isRole('inspector')) {
            $grid->model()->where('inspector', '=', Admin::user()->id);

            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableView();
            });
        } else if (Admin::user()->isRole('basic-user')) {
            $grid->actions(function ($actions) {

                $status = ((int)(($actions->row['status'])));
                if ($status == 4) {
                    $actions->disableDelete();
                    $actions->disableEdit();
                }
                if (
                    $status != 1
                ) {
                    $actions->disableDelete();
                    $actions->disableEdit();
                }
            });
        }



        $grid->column('id', __('Id'))->sortable();
        $grid->column('created_at', __('Created'))
            ->display(function ($item) {
                if (!$item) {
                    return "-";
                }
                return Carbon::parse($item)->toDateString();
            })->sortable();


        $grid->column('administrator_id', __('Applicant'))->display(function ($user) {
            $_user = Administrator::find($user);
            if (!$_user) {
                return "-";
            }
            return $_user->name;
        });

        $grid->column('filed_name', __('Field Name'))->sortable();
        $grid->column('name', __('Person responisble'))->sortable();
        $grid->column('size', __('Size'))->sortable();
        $grid->column('crop', __('Crop'))->sortable();
        $grid->column('variety', __('variety'))->sortable();
        $grid->column('district', __('District'))->sortable();
        $grid->column('subcourty', __('Subcouty'))->sortable();
        $grid->column('quantity_planted', __('Quantity planted'))->sortable();
        $grid->column('expected_yield', __('Expected yield'))->hide();
        $grid->column('phone_number', __('Phone number'))->hide();
        $grid->column('gps_latitude', __('Gps latitude'))->hide();
        $grid->column('gps_longitude', __('Gps longitude'))->hide();
        $grid->column('detail', __('Detail'))->hide();

        $grid->column('status_comment', __('Status comment'));

        $grid->column('inspector', __('Inspector'))->display(function ($userId) {
            if (Admin::user()->isRole('basic-user')) {
                return "-";
            }
            $u = Administrator::find($userId);
            if (!$u)
                return "Not assigned";
            return $u->name;
        })->sortable();

        $grid->column('status_comment', __('Status comment'))->hide();

        $grid->column('status', __('Status'))->display(function ($status) {
            return Utils::tell_status($status);
        })->sortable();

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
        $show = new Show(SubGrower::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('administrator_id', __('Administrator id'));
        $show->field('name', __('Name'));
        $show->field('size', __('Size'));
        $show->field('crop', __('Crop'));
        $show->field('variety', __('Variety'));
        $show->field('district', __('District'));
        $show->field('subcourty', __('Subcouty'));
        $show->field('planting_date', __('Planting date'));
        $show->field('quantity_planted', __('Quantity planted'));
        $show->field('expected_yield', __('Expected yield'));
        $show->field('phone_number', __('Phone number'));
        $show->field('gps_latitude', __('Gps latitude'));
        $show->field('gps_longitude', __('Gps longitude'));
        $show->field('detail', __('Detail'));
        $show->field('status', __('Status'));
        $show->field('inspector', __('Inspector'));
        $show->field('status_comment', __('Status comment'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new SubGrower());
        $user = Admin::user();

        if ($form->isCreating()) {
            $form->hidden('administrator_id')->default($user->id);
        };

        if (Admin::user()->isRole('basic-user')) {

            $form->text('name', __('Name'))->default($user->name)->required();
            $form->text('size', __('Garden Size (in Accre)'))->required();

            $form->select('crop', 'Crop')->options(Crop::all()->pluck('name', 'name'))
                ->required();

            $form->select('variety', 'Variety')->options(CropVariety::all()->pluck('name', 'name'))
                ->required();
            $form->text('filed_name', __('Filed name'))->required();
            $form->text('district', __('District'))->required();
            $form->text('subcourty', __('Subcourty'))->required();
            $form->text('village', __('Village'))->required();
            $form->date('planting_date', __('Planting date'))->required();
            $form->text('quantity_planted', __('Quantity planted'));
            $form->text('expected_yield', __('Expected yield'));
            $form->text('phone_number', __('Phone number'))->required();
            $form->text('gps_latitude', __('Gps latitude'))->required();
            $form->text('gps_longitude', __('Gps longitude'))->required();
            $form->textarea('detail', __('Detail'));
        }

        if (Admin::user()->isRole('inspector')) { 

            $form->radio('status', __('Review application'))
                ->options([
                    '5' => 'Accepted',
                    '3' => 'Halted',
                    '4' => 'Rejected',
                ])
                ->required() 
                ->when('in', [3, 4], function (Form $form) {
                    $form->textarea('status_comment', 'Enter status comment (Remarks)')
                        ->help("Please specify with a comment");
                });

            //$form->number('inspector', __('Inspector'));
            //$form->textarea('status_comment', __('Status comment'));
        }

        return $form;
    }
}