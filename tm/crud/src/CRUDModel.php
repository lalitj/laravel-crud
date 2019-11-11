<?php

namespace TM\Crud;

use App\FormModel;
use App\Members;
use App\User;
use Faker\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CRUDModel extends Model
{

    static $cash_options = ["" => "Select", "Bank Transfer" => "Bank Transfer", 'Cash' => 'Cash', "Cheque"=>"Cheque", "UPI" => "UPI", 'Wallet' => 'Wallet'];

    static public function form_2(){
        return [];
    }



    static function array_data($name = "name", $extra = [], $conditions = [], $id_key = "id"){


        //dd($conditions['where']);

        /*   $users = $this->users;*/

        /*$company_id = auth()->user()->company_id;

        if ($company_id == 3) {

            $data = static::select('name', 'id')->get();
        } else {
            $data = static::select('name', 'id')->where('id', $company_id)->get();
        }*/

        $cols = ["id", $name];
        if(count($extra)){
            $cols = array_merge($cols, $extra);
        }
        $data = static::select($cols);

        if(isset($conditions['where'])) {
            foreach ($conditions['where'] as $key => $val) {
                $data = $data->where($key, $val);
            }
        }
        $data = $data->get();
        //$data = $data->count();dd($data);

        $new_data = ["" => "Select"];
        foreach($data as $item){
            $extra_text = "";
            if(count($extra)){
                foreach($extra as $key){
                    $extra_text .= " - ".$item->$key;
                }
            }
            $new_data[$item->$id_key] = $item->$name . $extra_text;
        }

        return $new_data;

    }

    static function required_fields(){
        $form = static::form();
        $required_fields = [];
        foreach($form as $item){
            $key = $item['name'];
            if(isset($item['required']) && $item['required']){
                $required_fields[$key] = "required";
            }

            if(isset($item['validations'])){
                if(isset($required_fields[$key])){
                    $required_fields[$key] .= "|";
                    $required_fields[$key] .= $item['validations'];
                } else {
                    $required_fields[$key] = $item['validations'];
                }

            }
        }

        return $required_fields;
    }

    public static function edit_form($data){

        $form['fields'] = static::form();
        foreach($form['fields'] as $key => &$item){
            $item['value'] = $data->$key;
        }

        return $form;
    }

    static function form()
    {

        //$columns = DB::getSchemaBuilder()->getColumnListing('student');

        //$columns = array_flip($columns);

        $field =  [
            "type" => "text",
            "label" => "Full Name",
            "placeholder" => "",
            "text" => "",
            "value" => "",
            "required" => false
        ];
        $form = [];
        //unset($columns['id']);
        //unset($columns['created_at']);
        //unset($columns['updated_at']);


        /*foreach($columns as $key => $column){
            $field['label'] = studly_case($key);
            $form[$key] = $field;
        }*/

        $exclude = ['id', 'user_id', 'created_at', 'updated_at'];



        //dd($table_name);

        $table_name = str_plural(str_slug(class_basename(static::class)));
        if($table_name == "members"){
            $table_name = "users";
        }

        if(env('DB_CONNECTION') == "pgsql") {
            $columns = DB::select("SELECT * FROM information_schema.COLUMNS WHERE TABLE_NAME = '" . $table_name . "'");
            dd($columns);
            foreach ($columns as $value) {

                //echo "'" . $value->Field . "' => '" . $value->Type . "|" . ( $value->Null == "NO" ? 'required' : '' ) ."', <br/>" ;

                if(in_array($value->column_name, $exclude)) continue;

                $field['label'] = studly_case($value->column_name);
                $field['required'] = $value->is_nullable == "NO" ? true : false;
                if($value->data_type == "tinyint(1)"){
                    $field['type'] = "boolean";
                }

                $form[$value->column_name] = $field;

                //var_dump($value->Type);
                //var_dump($value->Type == "tinyint(1)");

                //echo "'" . $value->Field . "' => '" . $value->Type . "|" . ( $value->Null == "NO" ? 'required' : '' ) ."', <br/>" ;
            }

        } else {
            $columns = DB::select('show columns from ' . $table_name);
            foreach ($columns as $value) {

                //echo "'" . $value->Field . "' => '" . $value->Type . "|" . ( $value->Null == "NO" ? 'required' : '' ) ."', <br/>" ;

                if(in_array($value->Field, $exclude)) continue;

                $field['label'] = studly_case($value->Field);
                $field['required'] = $value->Null == "NO" ? true : false;
                if($value->Type == "tinyint(1)"){
                    $field['type'] = "boolean";
                }

                $form[$value->Field] = $field;

                //var_dump($value->Type);
                //var_dump($value->Type == "tinyint(1)");

                //echo "'" . $value->Field . "' => '" . $value->Type . "|" . ( $value->Null == "NO" ? 'required' : '' ) ."', <br/>" ;
            }
        }







        return $form;

    }

    static function object_data($name = "name"){
        /*   $users = $this->users;*/

        /*$company_id = auth()->user()->company_id;

        if ($company_id == 3) {

            $data = static::select('name', 'id')->get();
        } else {
            $data = static::select('name', 'id')->where('id', $company_id)->get();
        }*/

        $data = static::select('id','name')->get();
        /*$new_data[] = ["id" => "", "name" => "Select"];
        foreach($data as $item){
            $new_data[] = ["id" => $item->id, "name" => $item->name];
        }*/

        return $data;

    }

    static function multi_fields(){
        $fields = static::form();
        $multi = [];
        foreach($fields as $key => $field){
            if($field['type'] == "multi"){
                $multi[] = $key;
            }

        }

        return $multi;
    }

    static function correct_data($data){
        $fields = static::multi_fields();

        foreach($fields as $key => $field){

            if(is_array($data[$field])) {
                $data[$field] = implode(",", $data[$field]);
            }


        }

        return $data;
    }

    public static function fillForm($form){

        /*$browser->attach('resume', __DIR__ . '/images/sample.pdf');
        $browser->press('submit');
        return;*/



        $faker = Factory::create();

        foreach ($form as $key => &$field) {
            //$key = $field['name'];
            //if ((!isset($item['value']) || !$item["value"]) && strstr($item['name'],"_confirmation") === false) {
            //$error_text = str_replace('_', ' ', $item['name']);
            /*if($item['type'] == "radio"){
                $browser->type($item['name'], "test");
            } else {
                $browser->type($item['name'], "test");
            }*/

            switch ($field['type']){
                case "radio":
                    $array = array_flip($field["options"]);
                    $value = end($array);
                    break;
                case "select":
                    $array = array_flip($field["options"]);
                    $value = end($array);
                    break;
                case "date":
                    if(strstr($key,"birth") !== false){
                        $faker->date("Y-m-d", "2015-01-01");
                    }
                    $value = date("Y-m-d");
                    break;
                case "text":
                    $value = $field['label'];
                    break;
                case "phone":
                    $value = $faker->numberBetween(70000, 99999) . $faker->numberBetween(10000, 99999);
                    break;
                case "number":
                    if($field['name'] == "account_number") {
                        $value = $faker->numberBetween(20000, 99999) . $faker->numberBetween(10000, 99999) . $faker->numberBetween(100000, 999999);
                    }
                    break;
                case "email":
                    $value = $faker->email;
                    break;
                case "boolean":
                    $value = 1;
                    break;

                case "password":
                    $value = "123456";
                    break;

                case "default":
                    $value = "default";
                    break;
            }

            switch ($field['name']){
                case "name":
                    $value = $faker->name;
                    break;
                case "ref_id":
                    $random_child = User::child(auth()->id())->inRandomOrder()->first();
                    //dd($random_child);
                    if($random_child !== NULL) {
                        $value = $random_child->id;
                    } else {
                        $value = auth()->id();
                    }
                    break;
                case "aadhar_no":
                    $value = $faker->numberBetween(100000, 999999) . $faker->numberBetween(100000, 999999);
                    break;
                case "pan_no":
                    $value = generateRandomString(10);
                    break;
            }

            $field['value'] = $value;

        }

        return $form;

    }

    static function fields($field = ""){

        $fields = [
            'title' => [
                "type" => "text",
                "name" => "title",
                "label" => "Title",
                "placeholder" => "",
                "text" => "",
                "value" => "",
                "required" => true
            ],
            'name' => [
                "type" => "text",
                "name" => "name",
                "label" => "Name",
                "placeholder" => "",
                "text" => "",
                "value" => "",
                "required" => true
            ],
            'email' => [
                "type" => "email",
                "name" => "email",
                "label" => "E-Mail Address",
                "placeholder" => "",
                "text" => "",
                "value" => "",
            ],
            'password' => [
                "type" => "password",
                "name" => "password",
                "label" => "Password",
                "placeholder" => "",
                "text" => "",
                "value" => "",
                "validations" => "string|confirmed|min:6",
                "required" => true
            ],
            'password_confirmation' => [
                "type" => "password",
                "name" => "password_confirmation",
                "label" => "Confirm Password",
                "placeholder" => "",
                "text" => "",
                "value" => "",
                "required" => true
            ],
            'phone' => [
                "type" => "phone",
                "name" => "mobile_no",
                "label" => "Mobile Number",
                "placeholder" => "",
                "text" => "",
                "value" => "",
                "validations" => "digits:10",
                "required" => true
            ],
            'mobile_no' => [
                "type" => "phone",
                "name" => "mobile_no",
                "label" => "Mobile Number",
                "placeholder" => "",
                "text" => "",
                "value" => "",
                "validations" => "digits:10",
                "required" => true
            ],
            'aadhar_no' => [
                "type" => "text",
                "name" => "aadhar_no",
                "label" => "Aadhar Number",
                "placeholder" => "",
                "text" => "",
                "value" => "",
                "validations" => "digits:12",
                "required" => true
            ],
            'pan_no' => [
                "type" => "text",
                "name" => "pan_no",
                "label" => "Pan Number",
                "placeholder" => "",
                "text" => "",
                "value" => "",
                //"validations" => "digits:12",
                "required" => false
            ],
            'status' => [
                "type" => "boolean",
                "name" => "status",
                "label" => "Status",
                "placeholder" => "",
                "text" => "",
                "value" => "",
                "required" => true
            ],
            'direct_id' => [
                "type" => "text",
                "name" => "direct_id",
                "label" => "Sponsor ID",
                "placeholder" => "",
                "text" => "",
                "value" => "",
                "required" => true
            ],
            'ref_id' => [
                "type" => "text",
                "name" => "ref_id",
                "label" => "Parent ID",
                "placeholder" => "",
                "text" => "<a href='".route("tree.check")."'>Choose From Tree</a>",
                "value" => "",
                "required" => true
            ],
            'sponsor_name' => [
                "name"=>"sponsor_name",
                "type" => "text",
                "label" => "Sponsor Name",
                "text" => "",
                "placeholder" => "",
                "value"=>"",
                "required" =>""
            ],
            'plan_id' => [
                "type" => "select",
                "name" => "plan_id",
                "label" => config("custom.package_name"),
                "placeholder" => "",
                "text" => "",
                "options" => [],
                "required" => true,
                "value" => ""
            ],
            'side' => [
                "type" => "select",
                "name" => "side",
                "label" => "Side",
                "placeholder" => "",
                "text" => "",
                "value" => "",
                "options" => ["" => "Select", "1" => "Left", "2" => "Right"],
                "required" => true
            ],
            'payment_pin' => [
                "type" => "select",
                "name" => "payment_pin",
                "label" => "Payment Pin",
                "placeholder" => "",
                "text" => "",
                "value" => "",
                "class" => "selectize-create",
                "options" => [],
                "required" => true
            ],
            'user_id' => [
                "name" => "user_id",
                "type" => "select",
                "label" => "User",
                "text" => "",
                "value" => "",
                "options" => Members::array_data("unique_id", ["name"]),
                "required" => false,
                "class" => "selectize",
                "id" => "user_id"
            ],
            'source' => [
                "type" => "select",
                "name" => "source",
                "label" => "Source",
                "options" => self::$cash_options,
                "text" => "Amount: ₹",
                "placeholder" => "",
                "value" => [],
                "required" =>"true"
            ],
            'txn_id' => [
                "type" => "text",
                "name" => "txn_id",
                "label" => "Transaction ID",
                "text" => "",
                "placeholder" => "Add transaction ID if available",
                "value"=>""
            ],
            'name_title' =>
                FormModel::select("Title",[
                    "" => "Select",
                    "Mr." => "Mr.",
                    "Mrs." => "Mrs.",
                    "Miss." => "Miss."
                ])
            ,
            'relation_field' => FormModel::select("Relation Field",[
                    "" => "Select",
                    "Son Of" => "Son Of",
                    "Wife Of" => "Wife Of",
                    "Daughter Of" => "Daughter Of"
                ]),
            'relation_name' => FormModel::text("Relation Name"),
            'date_of_birth' => FormModel::text("Date Of Birth", false, "date"),
            'gender' => FormModel::select("Gender",[
                    "" => "Select",
                    "Male" => "Male",
                    "Female" => "Female",
                ]),
            'address' => FormModel::text("address", false, "textarea"),
            'marital_status' => FormModel::select("Marital Status",
                [
                    "" => "Select",
                    "Married" => "Married",
                    "Single" => "Single"
                ]),
            'nominee_name' => FormModel::text("Nominee Name"),
            'nominee_relation' => FormModel::text("Nominee Relation"),
        ];

        if($field && is_array($field)) {
            $output = [];
            foreach($field as $item){
                if(isset($fields[$item])){
                    $output[$item] = $fields[$item];
                    if($item == "plan_id"){
                        $output[$item]['options'] = Members::plan_options();
                    }
                }
            }
            return $output;
        }if($field && isset($fields[$field])){
            return $fields[$field];
        } else {
            return $fields;
        }


    }

}