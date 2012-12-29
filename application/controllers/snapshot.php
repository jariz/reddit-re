<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Jari
 * Date: 28-12-12
 * Time: 3:06
 * To change this template use File | Settings | File Templates.
 */
class snapshot extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->helper("file");
        $this->load->library(array());
    }

    public function index()
    {
        die(json_encode(array("error" => true, "msg" => "403")));
    }

    public function full($img)
    {
        if (in_array($img, get_filenames("static/snapshots"))) {
            $this->output->set_content_type("png")->set_output(read_file("static/snapshots/" . $img));
        } else show_404();
    }

    public function small()
    {
        $img = $this->uri->segment(2);
        if (in_array($img, get_filenames("static/snapshots"))) {
            $config['image_library'] = 'gd2';
            $config['source_image'] = 'static/snapshots/'.$img;
            $config['create_thumb'] = TRUE;
            $config['maintain_ratio'] = TRUE;
            $config['dynamic_output'] = true;
            $config['width'] = 290;
            $config["height"] = 205;
            $this->load->library('image_lib', $config);
            $this->image_lib->resize();
        } else show_404();
    }
}
