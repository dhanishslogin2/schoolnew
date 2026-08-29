<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Communication_setting_model extends CI_Model {

    protected $table = 'tbl_communication_settings';
    protected $primaryKey = 'setting_id';

    public function get_settings()
    {
        $settings = $this->db->where($this->primaryKey, 1)->get($this->table)->row();
        if (!$settings) {
            return (object)[
                'setting_id'                      => 1,
                'enable_inapp'                    => 1,
                'enable_sms'                      => 1,
                'enable_whatsapp'                 => 1,
                'enable_email'                    => 1,
                'sms_provider'                    => 'Generic SMS Gateway',
                'sms_sender_id'                   => 'SCHOLL',
                'whatsapp_provider'               => 'WhatsApp Business API',
                'email_from_name'                 => 'Login2 Management',
                'email_from_address'              => 'notifications@school.edu',
                'enable_scheduled_jobs'           => 1,
                'max_retries'                     => 3,
                'retry_interval_minutes'          => 15,
                'parent_teacher_direct_messaging' => 1
            ];
        }
        return $settings;
    }

    public function update_settings($data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where($this->primaryKey, 1)->update($this->table, $data);
    }

    public function get_audit_logs($limit = 30)
    {
        return $this->db
            ->select('log.*, s.full_name as user_name')
            ->from('tbl_communication_audit_logs log')
            ->join('tbl_staff s', 's.staff_id = log.user_id', 'left')
            ->order_by('log.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->result();
    }
}
