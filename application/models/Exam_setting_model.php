<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Exam_setting_model extends CI_Model {

    protected $table = 'tbl_examination_settings';
    protected $primaryKey = 'setting_id';

    public function get_settings()
    {
        $settings = $this->db->get($this->table)->row();
        if (!$settings) {
            $default = [
                'setting_id'                     => 1,
                'decimal_precision'              => 2,
                'default_max_marks'              => 100.00,
                'default_passing_marks'          => 35.00,
                'subject_pass_mark_rule'         => 1,
                'overall_pass_percentage'        => 35.00,
                'single_subject_fail_overall'    => 1,
                'rank_criteria'                  => 'Percentage',
                'include_failed_in_rank'         => 0,
                'show_rank_on_report_card'       => 1,
                'show_attendance_on_report_card' => 1,
                'report_card_header'             => 'School - Official Academic Report Card',
                'principal_signature_title'      => 'Principal',
                'teacher_signature_title'        => 'Class Teacher',
            ];
            $this->db->insert($this->table, $default);
            return (object)$default;
        }
        return $settings;
    }

    public function update_settings($data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $exists = $this->db->get($this->table)->row();
        if ($exists) {
            return $this->db->where($this->primaryKey, $exists->setting_id)->update($this->table, $data);
        }
        return $this->db->insert($this->table, $data);
    }
}
