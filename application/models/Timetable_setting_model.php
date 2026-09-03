<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Timetable_setting_model extends CI_Model {

    protected $table = 'tbl_timetable_settings';
    protected $primaryKey = 'setting_id';

    public function get_settings()
    {
        $settings = $this->db->where($this->primaryKey, 1)->get($this->table)->row();
        if (!$settings) {
            return (object)[
                'setting_id'              => 1,
                'working_days'            => 'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
                'max_periods_per_day'     => 8,
                'max_consecutive_periods' => 3,
                'allow_teacher_overlap'   => 0,
                'auto_publish'            => 0
            ];
        }
        return $settings;
    }

    public function get_working_days_array()
    {
        $settings = $this->get_settings();
        return array_map('trim', explode(',', $settings->working_days));
    }

    public function update_settings($data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where($this->primaryKey, 1)->update($this->table, $data);
    }

    public function get_publish_records($year_id)
    {
        return $this->db
            ->select('pub.*, c.class_name, div.division_name as division_name, div.division_name as section_name, s.full_name as publisher_name')
            ->from('tbl_timetable_publish pub')
            ->join('tbl_classes c', 'c.class_id = pub.class_id', 'left')
            ->join('tbl_divisions div', 'div.division_id = pub.section_id', 'left')
            ->join('tbl_staff s', 's.staff_id = pub.published_by', 'left')
            ->where('pub.academic_year_id', $year_id)
            ->get()
            ->result();
    }

    public function update_publish_status($year_id, $class_id, $section_id, $status, $user_id = NULL)
    {
        $existing = $this->db
            ->where('academic_year_id', $year_id)
            ->where('class_id', $class_id)
            ->where('division_id', $section_id)
            ->get('tbl_timetable_publish')
            ->row();

        $data = [
            'status'       => $status,
            'published_by' => $user_id,
            'updated_at'   => date('Y-m-d H:i:s')
        ];

        if ($status === 'Published') {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        if ($existing) {
            $this->db->where('publish_id', $existing->publish_id)->update('tbl_timetable_publish', $data);
        } else {
            $data['academic_year_id'] = $year_id;
            $data['class_id']         = $class_id;
            $data['division_id']       = $section_id;
            $data['created_at']       = date('Y-m-d H:i:s');
            $this->db->insert('tbl_timetable_publish', $data);
        }

        // Also update `is_locked` in tbl_timetable
        $is_locked = ($status === 'Locked') ? 1 : 0;
        $this->db
            ->where('academic_year_id', $year_id)
            ->where('class_id', $class_id)
            ->where('division_id', $section_id)
            ->update('tbl_timetable', ['is_locked' => $is_locked]);

        return TRUE;
    }
}
