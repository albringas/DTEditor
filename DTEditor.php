<?php

class DTEditor
{

    public function validate($datos,$options=array()){
        $ret = array();
        if (isset($options['required'])){
            foreach($options['required'] as $k=>$v){
                if (isset($datos[$v])){
                    if ($datos[$v]==""){
                        $ret['fieldErrors'][] = array("name"=>$v,"status"=>"Debes capturar un valor para este campo.");
                    }
                }
            }
        }
        return $ret;
    }

    public function ajax($tabla,$post,$idfield,$options=array()){

        $CI =& get_instance();
        $CI->load->database();
        $CI->load->helper('url');
        $CI->load->library('session');
        $CI->config->item('base_url');
        $database = $CI->db->database;
        $action = $post['action'];
        if ($action=="create"){
            // print_r($post['data'][0]);
            $datos = $post['data'][0];
            $val = $this->validate($datos, $options);
            if (count($val)>0){
                $ret = $val;
            }
            else {
                $CI->db->insert($tabla,$datos);
                $ret = $post['data'][0];
                $ret['DT_rowId'] = $ret[$idfield];
            }

            return json_encode($ret);
        }
        if ($action=="edit"){
            $keys = array_keys($post['data']);
            $id = $keys[0];
            $CI->db->where($idfield,(string) $id);
            $CI->db->update($tabla,$post['data'][$id]);
            $ret = $post['data'][$id];
            $ret['DT_rowId'] = $ret[$idfield];
            return json_encode($ret);
        }
        if ($action=="remove"){
            $keys = array_keys($post['data']);
            $id = $keys[0];

            $CI->db->query("delete from " . $tabla . " where " .
                $idfield . "='" . $id . "'");


            return "{ }";
        }

    }

}