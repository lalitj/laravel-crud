<?php

namespace TM\Crud;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class CRUDController extends Controller
{

    public $form = [];
    public $model = "";
    public $action = "";
    public $headings = [];
    public $dyna_settings = ['responsive' => 1];
    public $settings = ['edit' => 1, 'view' => false, 'title_field' => 'name'];
    public $stop = false;

    public $required_fields = "";
    public $redirect = "";
    public $keys = '*';
    public $show_keys = '*';
    public $headers = [];
    public $hide_created_at = "true";
    public $hide_updated_at = "true";

    public $dt_keys = [];
    public $dt_remove = [];
    public $dt_add = [];
    public $dt_query_name = "";
    public $join_tables = [];
    public $dt_filters = [];
    public $dt_settings = ['auto_foreign' => false];
    public $debug = true;

    public $showSubUnset = [];
    public $fill_form = false;
    public $views = ["layout" => "layouts.users", "index" => "tm::blocks.data"];



    public function init(){
        if(!$this->model){
            $class =  class_basename(get_called_class());
            $this->model =  str_replace("Controller", "",$class);
        }

        if(!$this->action){
            $this->action =  str_slug($this->model).".store";
        }

        if(!$this->redirect){
            $this->redirect = route(str_slug($this->model).".create");
        }
    }

    public function autoCrud(){
        if(!$this->model){
            $class =  class_basename(get_called_class());
            $this->model =  str_replace("Controller", "",$class);
        }

        if(!$this->action){

            $this->action =  str_slug($this->model);
        }

        if(!$this->redirect){
            $this->redirect = "/".str_slug($this->model)."/"."create/";
        }

        $table_name = str_plural(str_slug($this->model));
        if($table_name == "members"){
            $table_name = "users";
        }

        if(!count($this->dt_keys)){

            $columns = DB::select('show columns from ' . $table_name);

            $keys = [];
            foreach($columns as $column){
                $keys[] = $column->Field;
            }

            $keys = array_display_keys($keys,$this->dt_remove);
            //pre($keys,1);

            foreach ($keys as $key) {
                $this->dt_keys[$key] = title_string($key);
            }

            foreach ($this->dt_add as $key) {
                $this->dt_keys[$key] = title_string($key);
            }

            //pre($columns);
            /*foreach($columns as $column) {
                if($column->Field == "user_id"){
                    $this->dt_keys[$column->Field] = ["name" => "user.name", "key" => "user.name", "title" => "User"];
                    array_push($this->join_tables, 'user');
                }elseif($column->Key == "MUL" && $this->dt_settings['auto_foreign']){
                    //"user_id"=>["name" => "user.name", "key" => "user.name", "title" => "Users"],
                    $relation = "user";
                    $this->dt_keys[$column->Field] = ["name" => "$relation.name","key" => "$relation.name", "title" => title_case($relation)];
                    array_push($this->join_tables, $relation);
                } else {
                    $this->dt_keys[$column->Field] = title_case($column->Field);
                }

            }*/
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected function dyna(){

        /*if(Gate::denies('super-admin')){
            abort(403, "Sorry not sorry");
        }*/

        $data = "";
        if (!$data) {
            $this->init();
        }

        $model = "App\\" . $this->model;
        $action = $this->action;

        $keys = $this->keys;
        if (!$data) {
            $data = $model::select($keys)->get()->toArray();
        } elseif (!is_array($data)) {
            $data = $data->toArray();
        }

        $attributes = [
            'class' => 'table dynatable'//datatable
        ];
        if ($this->dyna_settings['responsive']) {
            $attributes['class'] .= " table-responsive";
        }


        foreach ($data as &$item) {
            if ($this->hide_created_at) unset($item['created_at']);
            if ($this->hide_updated_at) unset($item['updated_at']);


            /* if($this->model == "Bills") {
                 $item['print'] = "<a href='bill/print/" . $item["id"] . "'>View</a>";
             }
             if($this->model == "Items"){
                 $item['stocks'] = "<a href='items/" . $item["id"] . "'>View</a>";

             }*/
            if (array_key_exists('edit', $this->settings) && $this->settings['edit']) {
                $item['edit'] = "<a href='$action/" . $item["id"] . "/edit'>Edit</a>";
            }

            if (($keys != "*" && !(array_key_exists('hide_view', $this->settings) && $this->settings['hide_view'])) || (array_key_exists('view', $this->settings) && $this->settings['view'])) {
                //$item['view'] = "<a href='$action/" . $item["id"] .">View</a>";
                $item['view'] = "<a href='" . $action . "/" . $item["id"] . "'>View</a>";
            }


            /*if ($this->model == "Payment") {
                if ($item['status'] == 1) {
                    $item['status'] = 'Confirmed';
                } elseif ($item['status'] == 2) {
                    $item['status'] = 'Declined';
                } else {
                    $item['status'] = "<a class='confirm-prompt'  href='payment/" . $item['id'] . "/confirm'>Confirm Payment</a><br>";
                    $item['status'] .= "<br>";
                    $item['status'] .= "<a class='confirm-prompt' href='payment/" .$item['id']."/decline'>Decline Payment</a>";
                }
            }*/
        }

        /*if($this->model == "Items") {
            array_push($this->headers, "Stocks");
        }*/
        /*if($keys == "*" && count($data)){
          $first = $data[0];
            $headers =  array_keys($first);
            foreach($headers as &$item){
                $item = title_case($item);
                $item = str_replace("_", " ", $item);
            }
            //array_filter($headers,"title_case");
        } else {
            $headers = $this->headers;
        }*/
        if (!count($data)) {
            $headers = [];
        } else {
            $first = &$data[0];
            $headers = array_keys($first);
            foreach ($headers as &$item) {
                $item = title_case($item);
                $item = str_replace("_", " ", $item);
            }
        }
        return view('tm::forms.dyna',compact('items','headers','data','attributes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected function create()
    {
        $this->init();

        $args = func_get_args();
        if(count($args) > 0) {
            list($prefill_data) =  $args;
        }

        $model = "App\\" . $this->model;
        $action = $this->action;
        $view = $this->model;


         $form['fields'] = $model::form();

        if($this->fill_form) {
            $form['fields'] = $model::fillForm($form['fields']);
        }

        if(isset($prefill_data) && is_array($prefill_data) && count($prefill_data)) {
            foreach ($form['fields'] as $key => &$field) {
                if($field['type']== "multi" || $field['type'] == "select"){

                     $field['value'] = explode("," , $prefill_data[$key]);

                } else {
                    $field['value'] = $prefill_data[$key];
                }

            }
        }

       // $form['fields']['users']['value'] = explode(",", $form['fields']['users']['value']);
        //$form['fields']['projects']['value'] = explode(",", $form['fields']['projects']['value']);
        //$field['value'] = $prefill_data[$key];
        //return $form['fields'];
        $form['data']['action'] = $action;
        if (isset($this->headings['add'])) {
            $form['data']['heading'] = $this->headings['add'];
        } else {
            $form['data']['heading'] = "Add " . str_singular($this->model);
        }

        $form['data']['type'] = "add";
        $form['data']['submit'] = "Add";

        $layout = $this->views['layout'];

        return view('tm::forms.add',compact( "form", 'action','view','layout'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    protected function store(Request $request)
    {
        $this->init();

        $request = request()->all();
        $request = $this->store_before($request);
        if($this->stop){
            return $request;
        }

        $model = "App\\" . $this->model;
        $view = $this->model;

        $data = request()->all();

        $data = $model::correct_data($data);


        //return $model::required_fields();
        $this->validate(request(), $model::required_fields());

        if(array_key_exists('password', $data)){
            $data['password'] = Hash::make($data['password']);
        }
        $data = $this->save_before($data);
        $data = $model::create($data);
        /*if($this->redirect){

            return redirect($this->redirect);
        }*/
        /* return $data;*/
        $this->redirect = str_replace('arg_id', $data['id'], $this->redirect);


        return $this->store_after($request, $data, $view);


    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    /*private function show($id)
    {
        $this->init();

        $model = "App\\" . $this->model;

        $keys = $this->show_keys;
        if (!isset($data)) {
            $data = $model::select($keys)->find($id)->toArray();
        } elseif (!is_array($data)) {
            $data = $data->toArray();
        }

        //$items=$model::find($id);
        $attributes = [
            'class' => 'table'
        ];
        if ($this->dyna_settings['responsive']) {
            $attributes['class'] .= " table-responsive";
        }


        if ($this->hide_created_at) unset($data['created_at']);
        if ($this->hide_updated_at) unset($data['updated_at']);

        if (count($this->headers)) {
            $headers = $this->headers;
        } else {

            $headers = array_keys($data);
            foreach ($headers as &$item) {
                $item = title_case($item);
                $item = str_replace("_", " ", $item);
            }
        }

        $new_data = array_combine($headers, $data);
        unset($data);
        foreach ($new_data as $key => $item) {
            $data[] = [$key, $item];
        }

        $headers = ["Item", "Value"];


        //$data = $new_data;


        return view('forms.show', compact('items', 'headers', 'data', 'attributes'));


    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show_table($id)
    {
        $this->init();

        $args = func_get_args();

        if(count($args) == 2) {
            list($id, $data) = $args;
        }

        if (!isset($data)) {
            $model = "App\\" . $this->model;

            $keys = $this->show_keys;
            $data = $model::select($keys)->find($id)->toArray();

        } elseif (!is_array($data)) {
            $data = $data->toArray();
        }

        //$items=$model::find($id);
        $attributes = [
            'class' => 'table'
        ];
        if ($this->dyna_settings['responsive']) {
            $attributes['class'] .= " table-responsive";
        }


        if ($this->hide_created_at) unset($data['created_at']);
        if ($this->hide_updated_at) unset($data['updated_at']);

        if (count($this->headers)) {
            $headers = $this->headers;
        } else {

            $headers = array_keys($data);
            foreach ($headers as &$item) {
                $item = title_case($item);
                $item = str_replace("_", " ", $item);
            }
        }

        $new_data = array_combine($headers, $data);

        $data = $this->keyValue($new_data);

        $headers = ["Item", "Value"];


        //$data = $new_data;


        return view('tm::forms.show', compact('items', 'headers', 'data', 'attributes'));


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $this->init();

        $this->pre_edit($id);

        $model = "App\\" . $this->model;
        $data = $model::find($id);
        $action = $this->action;
        $view = $this->model;


        $form = $model::edit_form($data);

        $form['data']['action'] = $action . "/" . $id;
        $form['data']['heading'] = "Edit " . str_singular($this->model) . ": " . $id;
        $form['data']['type'] = "edit";
        $form['data']['submit'] = "Update";


        return view('tm::forms.add',compact( "form", 'action','view'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $this->init();

        $model = "App\\" . $this->model;
        $view = $this->model;
        $action = $this->action;
        $this->redirect = "/$action/" . $id . "/edit";
        $this->validate(request(), $model::required_fields());

        $update_data = request()->all();
        unset($update_data['_token']);
        unset($update_data['_method']);
        $data = $model::where('id', $id)->update($update_data);
        /*if($this->redirect){

            return redirect($this->redirect);
        }*/
        /* return $data;*/
        $this->redirect = str_replace('arg_id', $data['id'], $this->redirect);


        return $this->update_after($request, $data, $view);
        /* $this->init();

         $model = "App\\" . $this->model;
         $view = $this->model;



        $data=$model::where('id',$id)->update();
        return view('/');*/

        // $this->redirect = str_replace('arg_id',$data['id'], $this->redirect);


        //return $this->store_after($request, $data, $view);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * @param $new_data
     * @param $data
     * @param $key_prepend
     * @return array
     */
    public function keyValue($new_data, $key_prepend = "")
    {
        $data = [];
        foreach ($new_data as $key => $item) {
            if (!is_array($item)) {
                if(!$key_prepend || $key_prepend && !in_array($key, $this->showSubUnset)){
                    $data[] = [$key_prepend.$key, $item];
                }

            } else {
                if(is_string($key)){
                    $key_prepend .= str_singular($key)." 0";
                } else {
                    $key_prepend = substr($key_prepend, 0, strlen($key_prepend) - 2)." ";
                    $key_prepend .= (int) $key + 1 . " ";
                }
                $data = array_merge($data, $this->keyValue($item, $key_prepend));
            }
        }
        return $data;
    }

    public function csv($table)
    {
        if (($handle = fopen(public_path() . "/csv/" . $table .'.csv','r')) !== FALSE)
        {
            $data = fgetcsv($handle, 1000, ',');
            while (($data = fgetcsv($handle, 1000, ',')) !==FALSE)
            {

                $scifi = new \App\Items();
                $scifi->id = $data[0];
                $scifi->product_name = $data[1];
                //$scifi->potency = $data[1];
                $scifi->hsn_code = $data[2];
                $scifi->mfg_code = $data[3];
                $scifi->short_name = $data[4];
                $scifi->save();
            }
            fclose($handle);
        }

        return "Implemented";
    }

    public function search_ajax()
    {

        $search = request('term');


        /*if(Gate::denies('only-team')){
            abort(403, 'Sorry, not Sorry.');
        }*/

        /*if (isset($_GET['search'])) {
            return redirect("/search/" . $_GET['search']);
        }*/

        /*$user_id = request('user_id');
        if (!$user_id) $user_id = auth()->id();

        $user = User::find($user_id);
        $current_working = $user->current_working($user);*/

        //ready for deploy, In Development, ready for development

        if (env('DB_CONNECTION') == "pgsql") {
            $like_operator = "ilike";
        } else {
            $like_operator = "like";
        }

        $this->init();

        $model = "App\\" . $this->model;
        $field = "product_name";

        $data = $model::where($field, $like_operator, "%$search%")
            //->orderBy('project_id', 'asc')
            //->orderBy('priority', 'desc')
            //->orderBy('order', 'desc')
            ->orderBy($field, 'desc')
            ->limit(20)
            ->get();
        $output = [];

        foreach ($data as $item) {
            $label = $item->$field;
            if (strlen($label) > 50) {
                $label = substr($item->$field, 0, 50) . "... ";
            }
            //$label .= " - ".Task::statuses()[$item->status];
            $output[] = [
                "label" => $label,
                //"category" => $item->project->name,
                "id" => $item->id,
                //"status" => $item->status
            ];
        }
        return $output;



    }

    public function store_before($request){
        return $request;
    }

    public function save_before($data){
        return $data;
    }

    public function store_after($request, $data, $view){
        session()->flash('success', " $view Added successfully");

        return redirect($this->redirect);
    }

    public function update_after($request, $data, $view){
        session()->flash('success', " $view Updated successfully");

        return redirect($this->redirect);
    }

    public function pre_edit($id){

    }

    public function instant_clone($id){
        $this->init();

        //return $this->model;
        $model = "App\\" . $this->model;
        //$action = $this->action;
        //$this->redirect = "/$action";
        $update_data = $item_data = $model::where('id',$id)->first()->toArray();
        unset($update_data['created_at']);
        unset($update_data['updated_at']);
        unset($update_data['id']);
        $update_data['title'] .= $this->settings['title_field']." Clone";
        //$new = new Items()
        $update_data = $model::create($update_data);

        $message = str_singular($this->model).": ".$item_data['id'] ." Cloned successfully to ".str_singular($this->model).": ".$update_data['id'];
        session()->flash('success', $message);

        //echo str_slug($this->model);
        return redirect(str_slug($this->model));

        //return back();

        //$data->save();

    }

    public function edit_clone($id){
        $this->init();

        $model = "App\\" . $this->model;
        //$action = $this->action;
        //$this->redirect = "/$action";
         $update_data = $item_data = $model::where('id',$id)->first()->toArray();
        unset($update_data['created_at']);
        unset($update_data['updated_at']);
        unset($update_data['id']);
        // $update_data['product_name'] .= " Clone";
        //return $update_data;
        return $this->create($update_data);


        //$new = new Items()
        $update_data = $model::create($update_data);

        $message = str_singular($this->model).": ".$item_data['id'] ." Cloned successfully to ".str_singular($this->model).": ".$update_data['id'];
        session()->flash('success', $message);

        //echo str_slug($this->model);
        return redirect(str_slug($this->model));

        //return back();

        //$data->save();

    }

   /* public function index_data(Builder $builder)
    {
        $this->init();

        $model = "App\\" . $this->model;

        if (request()->ajax()) {

            return DataTables::of($model::query())->toJson();
        }

        $this->init();

        //return \Yajra\DataTables\Facades\DataTables::of(\App\User::query())->toJson();
        $keys = $this->keys;
        $data = $model::select($keys)->first()->toArray();

        $headers = [];
        foreach(array_keys($data) as $key) {

            $headers[] = ['data' => $key, 'name' => $key, 'title' => title_case($key)];

        }


        $html = $builder->columns($headers);

        return view('tm::forms.data', compact('html'));
    }*/

    public static function datatable_headers($data, $keys_array = [])
    {
        $headers = [];
        foreach (array_keys($data) as $key) {
            if(is_array($keys_array[$key])){
                $headers[] = [
                    'data' => $key,
                    'name' => $keys_array[$key]["name"],
                    'title' => isset($keys_array[$key]["title"]) ? $keys_array[$key]["title"] : title_case($key)
                ];
            } else {
                $headers[] = [
                    'data' => $key,
                    'name' => $key,
                    'title' => isset($keys_array[$key]) ? $keys_array[$key] : title_case($key)
                ];
            }
        }
        return $headers;
    }


    public static function datatable_headers_2($keys_array)
    {
        $headers = [];
        foreach (array_keys($keys_array) as $key) {
            if(is_array($keys_array[$key])){
                $headers[] = [
                    'data' => isset($keys_array[$key]["key"]) ? $keys_array[$key]["key"] : $key,
                    'name' => isset($keys_array[$key]["name"]) ? $keys_array[$key]["name"] : $key,
                    'title' => isset($keys_array[$key]["title"]) ? $keys_array[$key]["title"] : title_case($key),
                    'orderable' => true,
                    'searchable' => true
                ];
            } else {
                $headers[] = [
                    'data' => $key,
                    'name' => $key,
                    'title' => $keys_array[$key],
                    'orderable' => true,
                    'searchable' => true
                ];
            }

        }
        return $headers;
    }

    /*public function data(Builder $builder){
        $this->init();



        $data = $this->datatable($builder);
        if (request()->ajax() || 0) {
            return $data;
        }
        return view("tm::blocks.data", $data);
    }*/

    public function index(Builder $builder){

        $this->autoCrud();

        $data = $this->datatable($builder, $this->dt_query_name);
        if (request()->ajax() || 0) {
            return $data;
        }

        $data['layout'] = $this->views['layout'];

        return view($this->views["index"], $data);
    }

    public function datatable(Builder $builder){
        $this->init();
        $keys_array = $this->dt_keys;
        if(!count($keys_array)){
            dd("Setup dt_keys array");
        }
        $model = "App\\".$this->model;
        $joins = $this->join_tables;

        $args = func_get_args();
        $function = "";
        if(count($args) == 2) {
            list($x, $function) = $args;
        }

        $this->keys;
        if(isset($_GET['all'])) {
            $this->keys = "*";
        }

        $query = count($joins) ? $model::with($joins) : $model::select("*");

        if($function == "members") {
            $id = auth()->user()->id;
            $query->where("ref_id", $id);
        } elseif($function == "pins") {
            $user = auth()->user();
            $id = $user->id;
            $query->where("status", 1);
            if($user->type != "admin") {
                $query->where("for_user_id", $id);
            }
        } elseif($function == "admin-members") {
            $query->where("id", "<>", 1);
        } elseif($function == "earnings") {
            $user_id = auth()->user()->id;
            $date = Carbon::now()->format('Y-m-d');
            $query->where("user_id", $user_id);
            //$query->where("created_at",'<=', $date);
        }
        elseif($function == "pending-payouts") {
            $date = Carbon::now()->format('Y-m-d');
            $query->where("created_at",'<=', $date);
            $query->where("amount",'>', 0);
        }elseif($function == "admin-dashbaord") {
            //$tutor_id = auth()->user()->id;
            $query->where("tutor_id", " <> ", 0);
        } elseif($function == "admin-work-available") {
            //$tutor_id = auth()->user()->id;
            $query->where("tutor_id", 0);
        }

        foreach($this->dt_filters as $key => &$val){
            if(is_array($this->dt_keys[$key])){
                $val['title'] = $this->dt_keys[$key]['title'];
                $val['filter'] = str_replace(".","-",$this->dt_keys[$key]['name']);
            } else {
                $val['title'] = $this->dt_keys[$key];
                $val['filter'] = $key;

            }
        }

        if (request()->ajax() || 0) { //Data will be sent in json format for ajax requests

            $filters = request('filters');
            if(is_array($filters) && count($filters)){
                //pre($filters);
                foreach($filters as $key => $filter){
                    if(!isset($this->dt_filters[$key])){
                        if($this->debug) die("Filter $key sent from js but not set from controller");
                    } else {
                        $filter_data = $this->dt_filters[$key];
                        if($filter_data['type'] == "date-between"){
                            $start = $filter['start'];
                            $end = $filter['end'];
                            if($key == "date_submitted"){
                                $search_key = "created_at";
                            } else {
                                $search_key = $key;
                            }

                            $query->between($start, $end, $search_key);
                        }
                    }
                }
            }
            $ajax_data = $query;
            return DataTables::of(
                $ajax_data

            )
                /*->addColumn("status", function($data){
                    if(($data->status == 1) && auth()->user()->id == 1) {
                        return $data->status = "<a class='confirm-prompt' href='payment/" . $data->id . "/confirm'>Confirm Payment</a> <br> <a class='confirm-prompt' href='payment/" . $data->id . "/decline'>Decline Payment</a>";
                    }elseif(($data->status == 2) && auth()->user()->id==1){
                        return $data->status ="Confirmed";
                    } elseif($data->status == 3) {
                        return $data->status = "Declined";
                    } else {
                        return "User Not Active";
                    }

                })*/
                ->addColumn('view',function($data){
                    return "<a class='' href='/admin/paymentdetails/data/$data->id' class='btn-info family_data'>View</a>";
                })
                ->rawColumns(["status_action","view","status","monthly_income_status"])


                /*->filter(function ($query) {
                    if (request()->has('name')) {
                        $query->where('name', 'like', "%" . request('name') . "%");
                    }

                    if (request()->has('email')) {
                        $query->where('email', 'like', "%" . request('email') . "%");
                    }
                })*/
                /*->filterColumn('words', function($query, $keyword) {
                    $query->where("words", ">", $keyword);
                })*/
                ->toJson();

            /* ->addColumn("stocks", function (Items $item){
                     return "<a href='/items/".$item["id"]."'>View</a>";
                     //return view("blocks.button", compact('item'))->render();
                 })
                 ->addColumn("edit", function (Items $item){
                     return "<a href='/items/".$item["id"]."/edit'>Edit</a>";
                     //return view("blocks.button", compact('item'))->render();
                 })
                 ->rawColumns(["stocks", "edit"])*/

        }

        //return \Yajra\DataTables\Facades\DataTables::of(\App\User::query())->toJson();
        if(!$query->first()){
            $no_data = 1;
            return compact('no_data');
        }

        $data = $query->first()->toArray();
        if(isset($_GET['all'])) {
            $headers = $this->datatable_headers($data, $keys_array);
        }else {
            $headers =$this->datatable_headers_2($keys_array);
        }

        /*$headers = [[
            'data' => "file",
            'name' => "file",
            'title' => "Doc Name"
        ]];*/

        /* $headers[] = ['data' => "stocks", 'name' => "stocks", 'title' => "Stocks"];
         $headers[] = ['data' => "edit", 'name' => "edit", 'title' => "Edit"];*/

        /* $keys=Items::select($keys)->get()->toArray();
         foreach($keys as $key) {

              $data = array_keys($key);


             return $headers = [
                 ['data' => $data, 'name' => $data, 'title' => ''],
             ];
         }*/
        //$html = $datatables->getHtmlBuilder()->columns($columns);

        $html = $builder
            ->columns($headers)
            //->addCheckbox()
            ->parameters([
                'initComplete' => 'function() { 
                         this.api().columns().every(function () {
                            var column = this;
                            var input = document.createElement(\'input\');
                            $(input).appendTo($(column.footer()).empty())
                            .on(\'change\', function () {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());
            
                                column.search(val ? val : \'\', true, false).draw();
                            });
                        });
                     }',
            ]);

        $filters = $this->dt_filters;

        return compact('html','filters', 'headers');
        //return view('admin.dashboard', compact('html','filters', 'headers'));
        //return view('admin.data', compact('html'));
    }
    public function datatable_old(Builder $builder){


        $keys_array = $this->dt_keys;
        $model = "App\\".$this->model;
        $joins = $this->join_tables;

        $args = func_get_args();
        $function = "";
        if(count($args) == 2) {
            list($x, $function) = $args;
        }

        $this->keys;
        if(isset($_GET['all'])) {
            $this->keys = "*";
        }

        $query = count($joins) ? $model::with($joins) : $model::select("*");

        if($function == "student-dashboard") {
            $student_id = auth()->user()->id;
            $query->where("student_id", $student_id);
        } elseif($function == "student-credit") {
            $student_id = auth()->user()->id;
            $query->where("student_id", $student_id);
        } elseif($function == "admin-dashbaord") {
            //$tutor_id = auth()->user()->id;
            $query->where("tutor_id", " <> ", 0);
        } elseif($function == "admin-work-available") {
            //$tutor_id = auth()->user()->id;
            $query->where("tutor_id", 0);
        } elseif($function == "mentor-dashboard") {
            //$tutor_id = auth()->user()->id;
            $query->where("tutor_id", auth()->user()->tutor->id);
            $query->where("status","!=", env("STATUS_MARK_DONE"));
        }

        if (request()->ajax() || 0) { //Data will be sent in json format for ajax requests

            $filters = request('filters');
            if(count($filters)){
                //pre($filters);
                foreach($filters as $key => $filter){
                    if(!isset($this->dt_filters[$key])){
                        if($this->debug) die("Filter $key sent from js but not set from controller");
                    } else {
                        $filter_data = $this->dt_filters[$key];
                        if($filter_data['type'] == "date-between"){
                            $start = $filter['start'];
                            $end = $filter['end'];
                            if($key == "date_submitted"){
                                $search_key = "created_at";
                            } else {
                                $search_key = $key;
                            }

                            $query->between($start, $end, $search_key);
                        }
                    }
                }
            }

            /*$start = "1985-10-28";
            $end = "1985-10-28";
            $search_key = "date_assigned";
            //$query->between($start, $end, $search_key);*/
            $ajax_data = $query->get();

            $datatable_data = DataTables::of(
                $ajax_data

            )
                ->addColumn("upload_revision", function($document){
                    return view("columns.upload_revision", compact('document'));
                })
                ->addColumn("upload", function($data){
                    return "<a href='".route("document-review-create", ["id" => $data->id])."'>Upload</a>";
                })
                ->addColumn("assign", function ($data){
                    return "<a href='".route("assign-to-self", ["id" => $data->id])."'>Assign to self</a>";
                })
                ->addColumn("assign_tutor", function (){
                    return "<a href=''>Assign Tutor</a>";
                })
                ->addColumn("mark_as_final", function($data){
                    return "<a href='".route("mark-as-done", ["id" => $data->id])."'>Mark As Final</a>";
                })
                ->rawColumns(["mark_as_final","upload","assign","assign_tutor", "upload_revision"])


                /*->filter(function ($query) {
                    if (request()->has('name')) {
                        $query->where('name', 'like', "%" . request('name') . "%");
                    }

                    if (request()->has('email')) {
                        $query->where('email', 'like', "%" . request('email') . "%");
                    }
                })*/
                /*->filterColumn('words', function($query, $keyword) {
                    $query->where("words", ">", $keyword);
                })*/
                ->toJson();

            return $datatable_data;
            /* ->addColumn("stocks", function (Items $item){
                     return "<a href='/items/".$item["id"]."'>View</a>";
                     //return view("blocks.button", compact('item'))->render();
                 })
                 ->addColumn("edit", function (Items $item){
                     return "<a href='/items/".$item["id"]."/edit'>Edit</a>";
                     //return view("blocks.button", compact('item'))->render();
                 })
                 ->rawColumns(["stocks", "edit"])*/

        }

        //return \Yajra\DataTables\Facades\DataTables::of(\App\User::query())->toJson();
        if(!$query->first()){
            $no_data = 1;
            return compact('no_data');
        }

        $data = $query->first()->toArray();
        if(isset($_GET['all'])) {
            $headers = $this->datatable_headers($data, $keys_array);
        }else {
            $headers =$this->datatable_headers_2($keys_array);
        }

        /*$headers = [[
            'data' => "file",
            'name' => "file",
            'title' => "Doc Name"
        ]];*/

        /* $headers[] = ['data' => "stocks", 'name' => "stocks", 'title' => "Stocks"];
         $headers[] = ['data' => "edit", 'name' => "edit", 'title' => "Edit"];*/

        /* $keys=Items::select($keys)->get()->toArray();
         foreach($keys as $key) {

              $data = array_keys($key);


             return $headers = [
                 ['data' => $data, 'name' => $data, 'title' => ''],
             ];
         }*/
        //$html = $datatables->getHtmlBuilder()->columns($columns);

        $html = $builder
            ->columns($headers)
            ->addCheckbox()
            ->parameters([
                'initComplete' => 'function() { 
                         this.api().columns().every(function () {
                            var column = this;
                            var input = document.createElement(\'input\');
                            $(input).appendTo($(column.footer()).empty())
                            .on(\'change\', function () {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());
            
                                column.search(val ? val : \'\', true, false).draw();
                            });
                        });
                     }',
            ]);

        $filters = $this->dt_filters;

        return compact('html','filters', 'headers');
        //return view('admin.dashboard', compact('html','filters', 'headers'));
        //return view('admin.data', compact('html'));
    }
}
