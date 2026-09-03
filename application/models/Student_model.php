<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Student_model extends CI_Model {

    protected $table = 'tbl_students';
    protected $primaryKey = 'student_id';

    public function get_dashboard_stats($year_id = NULL)
    {
        $year_id = $year_id ? (int)$year_id : get_current_academic_year_id();

        $total_students = $this->db->where('is_deleted', 'n')->where('academic_year_id', $year_id)->count_all_results('tbl_students');
        $active_students = $this->db->where('status', 1)->where('is_deleted', 'n')->where('academic_year_id', $year_id)->count_all_results('tbl_students');
        $inactive_students = $this->db->where('status', 0)->where('is_deleted', 'n')->where('academic_year_id', $year_id)->count_all_results('tbl_students');
        
        $male_students = $this->db->where('gender', 'Male')->where('status', 1)->where('is_deleted', 'n')->where('academic_year_id', $year_id)->count_all_results('tbl_students');
        $female_students = $this->db->where('gender', 'Female')->where('status', 1)->where('is_deleted', 'n')->where('academic_year_id', $year_id)->count_all_results('tbl_students');

        // New admissions
        $new_admissions_count = $this->db->where('status', 1)->where('is_deleted', 'n')->where('academic_year_id', $year_id)->count_all_results('tbl_students');

        // Class-wise breakdown
        $class_counts = $this->db->query("
            SELECT c.class_id, c.class_name, 
                   COUNT(st.student_id) as student_count,
                   SUM(CASE WHEN st.gender = 'Male' THEN 1 ELSE 0 END) as male_count,
                   SUM(CASE WHEN st.gender = 'Female' THEN 1 ELSE 0 END) as female_count
            FROM tbl_classes c
            LEFT JOIN tbl_students st ON st.class_id = c.class_id AND st.status = 1 AND st.is_deleted = 'n' AND st.academic_year_id = ?
            WHERE c.status = 1 AND c.is_deleted = 'n' AND c.academic_year_id = ?
            GROUP BY c.class_id
            ORDER BY c.class_id ASC
        ", [$year_id, $year_id])->result();

        // Recent admissions
        $recent_admissions = $this->db
            ->select('st.*, c.class_name, div.division_name as division_name, div.division_name as section_name')
            ->from('tbl_students st')
            ->join('tbl_classes c', 'c.class_id = st.class_id', 'left')
            ->join('tbl_divisions div', 'div.division_id = st.division_id', 'left')
            ->where('st.status', 1)
            ->where('st.is_deleted', 'n')
            ->where('st.academic_year_id', $year_id)
            ->order_by('st.student_id', 'DESC')
            ->limit(8)
            ->get()
            ->result();

        return (object)[
            'total_students'    => $total_students,
            'active_students'   => $active_students,
            'inactive_students' => $inactive_students,
            'male_students'     => $male_students,
            'female_students'   => $female_students,
            'new_admissions'    => $new_admissions_count,
            'class_counts'      => $class_counts,
            'recent_admissions' => $recent_admissions,
        ];
    }

    public function get_all($filters = array(), $limit = NULL, $offset = NULL)
    {
        $this->db
            ->select("st.*, c.class_name, COALESCE(div.division_name, 'A') as division_name, COALESCE(div.division_name, 'A') as section_name, y.year_name")
            ->from('tbl_students st')
            ->join('tbl_classes c', 'c.class_id = st.class_id', 'left')
            ->join('tbl_divisions div', 'div.division_id = st.division_id', 'left')
            ->join('tbl_academic_years y', 'y.academic_year_id = st.academic_year_id', 'left')
            ->where('st.status >=', 0)
            ->where('st.is_deleted', 'n')
            ->order_by('st.student_id', 'ASC');

        if (!empty($filters['academic_year_id'])) {
            $this->db->where('st.academic_year_id', (int)$filters['academic_year_id']);
        }
        if (!empty($filters['class_id'])) {
            $this->db->where('st.class_id', (int)$filters['class_id']);
        }
        if (!empty($filters['division_id'])) {
            $default_sec_id = $this->Division_model->get_default_division_id(!empty($filters['class_id']) ? (int)$filters['class_id'] : null);
            $this->db->group_start()
                ->where('st.division_id', (int)$filters['division_id']);
            if ((int)$filters['division_id'] === (int)$default_sec_id) {
                $this->db->or_where('st.division_id IS NULL', null, false)
                         ->or_where('st.division_id', 0);
            }
            $this->db->group_end();
        }
        if (!empty($filters['gender'])) {
            $this->db->where('st.gender', $filters['gender']);
        }
        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== 'All') {
            $this->db->where('st.status', (int)$filters['status']);
        }
        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $this->db->group_start()
                ->like('st.first_name', $s)
                ->or_like('st.last_name', $s)
                ->or_like('st.admission_number', $s)
                ->or_like('st.guardian_name', $s)
                ->or_like('st.guardian_phone', $s)
                ->or_like('st.roll_number', $s)
                ->group_end();
        }

        if ($limit !== NULL && $limit > 0) {
            $this->db->limit($limit, $offset ?: 0);
        }

        return $this->db->get()->result();
    }

    public function count_filtered($filters = array())
    {
        $this->db
            ->from('tbl_students st')
            ->where('st.status >=', 0)
            ->where('st.is_deleted', 'n');

        if (!empty($filters['academic_year_id'])) {
            $this->db->where('st.academic_year_id', (int)$filters['academic_year_id']);
        }
        if (!empty($filters['class_id'])) {
            $this->db->where('st.class_id', (int)$filters['class_id']);
        }
        if (!empty($filters['division_id'])) {
            $default_sec_id = $this->Division_model->get_default_division_id(!empty($filters['class_id']) ? (int)$filters['class_id'] : null);
            $this->db->group_start()
                ->where('st.division_id', (int)$filters['division_id']);
            if ((int)$filters['division_id'] === (int)$default_sec_id) {
                $this->db->or_where('st.division_id IS NULL', null, false)
                         ->or_where('st.division_id', 0);
            }
            $this->db->group_end();
        }
        if (!empty($filters['gender'])) {
            $this->db->where('st.gender', $filters['gender']);
        }
        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== 'All') {
            $this->db->where('st.status', (int)$filters['status']);
        }
        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $this->db->group_start()
                ->like('st.first_name', $s)
                ->or_like('st.last_name', $s)
                ->or_like('st.admission_number', $s)
                ->or_like('st.guardian_name', $s)
                ->or_like('st.guardian_phone', $s)
                ->or_like('st.roll_number', $s)
                ->group_end();
        }

        return $this->db->count_all_results();
    }

    public function get_datatables_data($filters = array(), $limit = 25, $start = 0, $order_col = 'st.student_id', $order_dir = 'ASC')
    {
        $allowed_cols = array(
            0 => 'st.admission_number',
            1 => 'st.first_name',
            2 => 'c.class_name',
            3 => 'st.gender',
            4 => 'st.date_of_birth',
            5 => 'st.guardian_name',
            6 => 'st.guardian_phone',
            7 => 'st.status',
            8 => 'st.student_id'
        );

        $col = isset($allowed_cols[$order_col]) ? $allowed_cols[$order_col] : 'st.student_id';
        $dir = (strtoupper($order_dir) === 'DESC') ? 'DESC' : 'ASC';

        $this->db
            ->select('st.student_id, st.admission_number, st.roll_number, st.first_name, st.last_name, 
                      st.gender, st.date_of_birth, st.guardian_name, st.guardian_phone, st.status, st.photo,
                      c.class_name, div.division_name as division_name, div.division_name as section_name, y.year_name')
            ->from('tbl_students st')
            ->join('tbl_classes c', 'c.class_id = st.class_id', 'left')
            ->join('tbl_divisions div', 'div.division_id = st.division_id', 'left')
            ->join('tbl_academic_years y', 'y.academic_year_id = st.academic_year_id', 'left')
            ->where('st.is_deleted', 'n');

        if (!empty($filters['academic_year_id'])) {
            $this->db->where('st.academic_year_id', (int)$filters['academic_year_id']);
        }
        if (!empty($filters['class_id'])) {
            $this->db->where('st.class_id', (int)$filters['class_id']);
        }
        if (!empty($filters['division_id'])) {
            $this->db->where('st.division_id', (int)$filters['division_id']);
        }
        if (!empty($filters['gender'])) {
            $this->db->where('st.gender', $filters['gender']);
        }
        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== 'All') {
            $this->db->where('st.status', (int)$filters['status']);
        }
        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $this->db->group_start()
                ->like('st.first_name', $s)
                ->or_like('st.last_name', $s)
                ->or_like('st.admission_number', $s)
                ->or_like('st.guardian_name', $s)
                ->or_like('st.guardian_phone', $s)
                ->or_like('st.roll_number', $s)
                ->or_like('c.class_name', $s)
                ->or_like('div.division_name as division_name, div.division_name as section_name', $s)
            ->group_end();
        }

        $this->db->order_by($col, $dir);

        if ($limit > 0) {
            $this->db->limit($limit, $start);
        }

        return $this->db->get()->result();
    }

    public function get_datatables_count_all($filters = array())
    {
        $this->db->where('is_deleted', 'n');
        if (!empty($filters['academic_year_id'])) {
            $this->db->where('academic_year_id', (int)$filters['academic_year_id']);
        }
        return $this->db->count_all_results('tbl_students');
    }

    public function get_by_id($id)
    {
        return $this->db
            ->select('st.*, c.class_name, div.division_name as division_name, div.division_name as section_name, y.year_name')
            ->from('tbl_students st')
            ->join('tbl_classes c', 'c.class_id = st.class_id', 'left')
            ->join('tbl_divisions div', 'div.division_id = st.division_id', 'left')
            ->join('tbl_academic_years y', 'y.academic_year_id = st.academic_year_id', 'left')
            ->where('st.student_id', $id)
            ->get()
            ->row();
    }

    public function get_profile($id)
    {
        $student = $this->get_by_id($id);
        if (!$student) return NULL;

        // 1. Documents
        $student->documents = $this->db
            ->where('student_id', $id)
            ->where('status', 1)
            ->order_by('document_id', 'DESC')
            ->get('tbl_student_documents')
            ->result();

        // 2. Promotions & Academic History
        $student->promotions = $this->db
            ->select('p.*, fy.year_name as from_year, ty.year_name as to_year, fc.class_name as from_class, tc.class_name as to_class, fdiv.division_name as division_name, div.division_name as section_name as from_section, tdiv.division_name as division_name, div.division_name as section_name as to_section')
            ->from('tbl_student_promotions p')
            ->join('tbl_academic_years fy', 'fy.academic_year_id = p.from_academic_year_id', 'left')
            ->join('tbl_academic_years ty', 'ty.academic_year_id = p.to_academic_year_id', 'left')
            ->join('tbl_classes fc', 'fc.class_id = p.from_class_id', 'left')
            ->join('tbl_classes tc', 'tc.class_id = p.to_class_id', 'left')
            ->join('tbl_divisions fdiv', 'fdiv.division_id = p.from_division_id', 'left')
            ->join('tbl_divisions tdiv', 'tdiv.division_id = p.to_division_id', 'left')
            ->where('p.student_id', $id)
            ->order_by('p.promotion_date', 'DESC')
            ->get()
            ->result();

        // 3. Transfer / TC status
        $student->transfer = $this->db
            ->select('t.*, c.class_name as prev_class_name, y.year_name')
            ->from('tbl_student_transfers t')
            ->join('tbl_classes c', 'c.class_id = t.previous_class_id', 'left')
            ->join('tbl_academic_years y', 'y.academic_year_id = t.academic_year_id', 'left')
            ->where('t.student_id', $id)
            ->order_by('t.transfer_id', 'DESC')
            ->get()
            ->row();

        // 4. Attendance Summary
        $this->load->model('Attendance_model');
        $student->attendance = $this->Attendance_model->get_student_profile_attendance($id);

        // 5. Fees & Finance Summary
        $this->load->model('Fee_model');
        $student->fee_profile = $this->Fee_model->get_student_fee_profile($id);

        return $student;
    }

    /* =========================================================================
       Documents Management
       ========================================================================= */
    public function get_all_documents($filters = array())
    {
        $this->db
            ->select('d.*, st.admission_number, st.first_name, st.last_name, c.class_name, div.division_name as division_name, div.division_name as section_name')
            ->from('tbl_student_documents d')
            ->join('tbl_students st', 'st.student_id = d.student_id', 'left')
            ->join('tbl_classes c', 'c.class_id = st.class_id', 'left')
            ->join('tbl_divisions div', 'div.division_id = st.division_id', 'left')
            ->where('d.status', 1)
            ->order_by('d.document_id', 'DESC');

        if (!empty($filters['student_id'])) {
            $this->db->where('d.student_id', $filters['student_id']);
        }
        if (!empty($filters['document_type'])) {
            $this->db->where('d.document_type', $filters['document_type']);
        }
        if (!empty($filters['student_id'])) {
            $this->db->where('d.student_id', $filters['student_id']);
        }
        if (!empty($filters['document_type'])) {
            $this->db->where('d.document_type', $filters['document_type']);
        }
        if (!empty($filters['class_id'])) {
            $this->db->where('st.class_id', $filters['class_id']);
        }
        if (!empty($filters['academic_year_id'])) {
            $this->db->where('st.academic_year_id', $filters['academic_year_id']);
        }

        return $this->db->get()->result();
    }

    public function add_document($data)
    {
        $this->db->insert('tbl_student_documents', $data);
        return $this->db->insert_id();
    }

    public function delete_document($document_id)
    {
        return $this->db
            ->where('document_id', $document_id)
            ->update('tbl_student_documents', ['is_deleted' => 'y', 'status' => 0]);
    }

    /* =========================================================================
       Promotions Engine
       ========================================================================= */
    public function promote_students($student_ids, $from_year, $from_class, $from_sec, $to_year, $to_class, $to_sec, $type = 'Promoted', $remarks = '')
    {
        if (empty($student_ids) || !is_array($student_ids)) return FALSE;

        $this->db->trans_start();

        foreach ($student_ids as $sid) {
            $orig_from_sec = $from_sec;
            if (empty($orig_from_sec)) {
                $st = $this->db->select('division_id')->where('student_id', (int)$sid)->get('tbl_students')->row();
                $orig_from_sec = ($st && !empty($st->section_id)) ? $st->section_id : NULL;
            }

            // Record promotion history
            $this->db->insert('tbl_student_promotions', array(
                'student_id'            => (int)$sid,
                'from_academic_year_id' => (int)$from_year,
                'from_class_id'         => (int)$from_class,
                'from_division_id'       => $orig_from_sec ? (int)$orig_from_sec : NULL,
                'to_academic_year_id'   => (int)$to_year,
                'to_class_id'           => (int)$to_class,
                'to_division_id'         => (int)$to_sec,
                'promotion_date'        => date('Y-m-d'),
                'promotion_type'        => $type,
                'remarks'               => $remarks ?: 'Promoted to new academic session'
            ));

            // Update current student record
            $this->db->where('student_id', (int)$sid)->update('tbl_students', array(
                'academic_year_id' => (int)$to_year,
                'class_id'         => (int)$to_class,
                'division_id'       => (int)$to_sec,
                'updated_at'       => date('Y-m-d H:i:s')
            ));
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function get_promotions($filters = array())
    {
        $this->db
            ->select('p.*, st.admission_number, st.first_name, st.last_name, fy.year_name as from_year, ty.year_name as to_year, fc.class_name as from_class, tc.class_name as to_class, fdiv.division_name as division_name, div.division_name as section_name as from_section, tdiv.division_name as division_name, div.division_name as section_name as to_section')
            ->from('tbl_student_promotions p')
            ->join('tbl_students st', 'st.student_id = p.student_id', 'left')
            ->join('tbl_academic_years fy', 'fy.academic_year_id = p.from_academic_year_id', 'left')
            ->join('tbl_academic_years ty', 'ty.academic_year_id = p.to_academic_year_id', 'left')
            ->join('tbl_classes fc', 'fc.class_id = p.from_class_id', 'left')
            ->join('tbl_classes tc', 'tc.class_id = p.to_class_id', 'left')
            ->join('tbl_divisions fdiv', 'fdiv.division_id = p.from_division_id', 'left')
            ->join('tbl_divisions tdiv', 'tdiv.division_id = p.to_division_id', 'left')
            ->order_by('p.promotion_id', 'DESC');

        if (!empty($filters['student_id'])) {
            $this->db->where('p.student_id', $filters['student_id']);
        }
        if (!empty($filters['academic_year_id'])) {
            $this->db->group_start()
                ->where('p.from_academic_year_id', $filters['academic_year_id'])
                ->or_where('p.to_academic_year_id', $filters['academic_year_id'])
                ->group_end();
        }

        return $this->db->get()->result();
    }

    /* =========================================================================
       Transfer / TC Management
       ========================================================================= */
    public function get_transfers($filters = array())
    {
        $this->db
            ->select('t.*, st.admission_number, st.first_name, st.last_name, st.gender, st.date_of_birth, st.guardian_name, c.class_name as prev_class, y.year_name')
            ->from('tbl_student_transfers t')
            ->join('tbl_students st', 'st.student_id = t.student_id', 'left')
            ->join('tbl_classes c', 'c.class_id = t.previous_class_id', 'left')
            ->join('tbl_academic_years y', 'y.academic_year_id = t.academic_year_id', 'left')
            ->order_by('t.transfer_id', 'DESC');

        if (!empty($filters['status'])) {
            $this->db->where('t.status', $filters['status']);
        }
        if (!empty($filters['academic_year_id'])) {
            $this->db->where('t.academic_year_id', $filters['academic_year_id']);
        }

        return $this->db->get()->result();
    }

    public function get_transfer_by_id($transfer_id)
    {
        return $this->db
            ->select('t.*, st.admission_number, st.first_name, st.last_name, st.gender, st.date_of_birth, st.guardian_name, st.guardian_relation, st.address, st.roll_number, c.class_name as prev_class, y.year_name')
            ->from('tbl_student_transfers t')
            ->join('tbl_students st', 'st.student_id = t.student_id', 'left')
            ->join('tbl_classes c', 'c.class_id = t.previous_class_id', 'left')
            ->join('tbl_academic_years y', 'y.academic_year_id = t.academic_year_id', 'left')
            ->where('t.transfer_id', $transfer_id)
            ->get()
            ->row();
    }

    public function issue_transfer($data)
    {
        $this->db->trans_start();

        // 1. Insert into tbl_student_transfers
        $this->db->insert('tbl_student_transfers', $data);
        $transfer_id = $this->db->insert_id();

        // 2. Also register in tbl_certificates for school record consistency
        $this->db->insert('tbl_certificates', array(
            'student_id'       => $data['student_id'],
            'certificate_type' => 'Transfer Certificate',
            'certificate_no'   => $data['tc_number'],
            'issue_date'       => $data['transfer_date'],
            'remarks'          => $data['reason'],
            'status'           => 1
        ));

        // 3. Update student status to 0 (Inactive/Transferred)
        $this->db->where('student_id', $data['student_id'])->update('tbl_students', array(
            'status'     => 0,
            'updated_at' => date('Y-m-d H:i:s')
        ));

        $this->db->trans_complete();
        return ($this->db->trans_status()) ? $transfer_id : FALSE;
    }

    /* =========================================================================
       Admission Management
       ========================================================================= */
    public function get_admissions($filters = array())
    {
        $this->db
            ->select('a.*, c.class_name, div.division_name as division_name, div.division_name as section_name, y.year_name')
            ->from('tbl_admissions a')
            ->join('tbl_classes c', 'c.class_id = a.class_id', 'left')
            ->join('tbl_divisions div', 'div.division_id = a.division_id', 'left')
            ->join('tbl_academic_years y', 'y.academic_year_id = a.academic_year_id', 'left')
            ->order_by('a.admission_id', 'DESC');

        if (!empty($filters['status']) && $filters['status'] !== 'All') {
            $this->db->where('a.status', $filters['status']);
        }
        if (!empty($filters['class_id'])) {
            $this->db->where('a.class_id', $filters['class_id']);
        }
        if (!empty($filters['academic_year_id'])) {
            $this->db->where('a.academic_year_id', $filters['academic_year_id']);
        }
        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $this->db->group_start()
                ->like('a.first_name', $s)
                ->or_like('a.last_name', $s)
                ->or_like('a.application_number', $s)
                ->or_like('a.guardian_name', $s)
                ->or_like('a.guardian_phone', $s)
                ->group_end();
        }

        return $this->db->get()->result();
    }

    public function get_admission_by_id($admission_id)
    {
        return $this->db
            ->select('a.*, c.class_name, div.division_name as division_name, div.division_name as section_name, y.year_name')
            ->from('tbl_admissions a')
            ->join('tbl_classes c', 'c.class_id = a.class_id', 'left')
            ->join('tbl_divisions div', 'div.division_id = a.division_id', 'left')
            ->join('tbl_academic_years y', 'y.academic_year_id = a.academic_year_id', 'left')
            ->where('a.admission_id', $admission_id)
            ->get()
            ->row();
    }

    public function add_admission($data)
    {
        $this->db->insert('tbl_admissions', $data);
        return $this->db->insert_id();
    }

    public function update_admission_status($admission_id, $status, $student_id = NULL)
    {
        $updateData = array('status' => $status);
        if ($student_id) $updateData['student_id'] = $student_id;
        return $this->db->where('admission_id', $admission_id)->update('tbl_admissions', $updateData);
    }

    public function convert_admission_to_student($admission_id, $section_id, $roll_number = NULL)
    {
        $adm = $this->get_admission_by_id($admission_id);
        if (!$adm) return FALSE;

        $admission_number = 'EDU' . date('Y') . sprintf('%03d', rand(100, 999));
        $studentData = array(
            'admission_number' => $admission_number,
            'first_name'       => $adm->first_name,
            'last_name'        => $adm->last_name,
            'gender'           => $adm->gender,
            'date_of_birth'    => $adm->date_of_birth,
            'blood_group'      => $adm->blood_group,
            'academic_year_id' => $adm->academic_year_id,
            'class_id'         => $adm->class_id,
            'division_id'       => $section_id ?: 1,
            'roll_number'      => $roll_number,
            'guardian_name'    => $adm->guardian_name,
            'guardian_relation'=> $adm->guardian_relation ?: 'Father',
            'guardian_phone'   => $adm->guardian_phone,
            'guardian_email'   => $adm->guardian_email,
            'address'          => $adm->address,
            'status'           => 1,
            'created_at'       => date('Y-m-d H:i:s')
        );

        $this->db->trans_start();
        $this->db->insert('tbl_students', $studentData);
        $student_id = $this->db->insert_id();

        $this->db->where('admission_id', $admission_id)->update('tbl_admissions', array(
            'status'     => 'Admitted',
            'student_id' => $student_id,
            'division_id' => $section_id ?: 1
        ));

        $this->db->trans_complete();
        return ($this->db->trans_status()) ? $student_id : FALSE;
    }

    public function count_students($academic_year_id = NULL)
    {
        if ($academic_year_id) {
            $this->db->where('academic_year_id', $academic_year_id);
        }
        return $this->db
            ->where('status', 1)
            ->count_all_results($this->table);
    }

    public function get_gender_stats()
    {
        $boys = $this->db
            ->where('gender', 'Male')
            ->where('status', 1)
            ->count_all_results($this->table);

        $girls = $this->db
            ->where('gender', 'Female')
            ->where('status', 1)
            ->count_all_results($this->table);

        return array('boys' => $boys, 'girls' => $girls);
    }

    public function get_new_admissions_count($month = NULL, $year = NULL)
    {
        if (!$month) $month = date('m');
        if (!$year) $year = date('Y');

        return $this->db
            ->where('MONTH(created_at)', $month)
            ->where('YEAR(created_at)', $year)
            ->where('status', 1)
            ->count_all_results($this->table);
    }

    public function get_grade_distribution()
    {
        return $this->db
            ->select('c.class_name, COUNT(st.student_id) as count')
            ->from('tbl_classes c')
            ->join('tbl_students st', 'st.class_id = c.class_id AND st.status = 1', 'left')
            ->where('c.status', 1)
            ->group_by('c.class_id')
            ->order_by('c.class_id', 'ASC')
            ->limit(4)
            ->get()
            ->result();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        return $this->db
            ->where($this->primaryKey, $id)
            ->update($this->table, $data);
    }

    public function soft_delete($id)
    {
        return $this->db
            ->where($this->primaryKey, $id)
            ->update($this->table, ['status' => 0, 'is_deleted' => 'y']);
    }

    /**
     * Get classes with active student count for a specific academic year.
     *
     * @param int $academic_year_id
     * @return array
     */
    public function get_classes_with_student_count($academic_year_id, $status = 1)
    {
        $academic_year_id = (int)$academic_year_id;
        $status_sql = "";
        $params = [$academic_year_id];

        if ($status !== NULL && $status !== '' && $status !== 'All') {
            $status_sql = " AND st.status = ? ";
            $params[] = (int)$status;
        }
        $params[] = $academic_year_id;

        return $this->db->query("
            SELECT c.class_id, c.class_name, c.class_code, c.academic_year_id,
                   COUNT(DISTINCT st.student_id) as total_students,
                   SUM(CASE WHEN st.status = 1 THEN 1 ELSE 0 END) as active_students,
                   SUM(CASE WHEN st.gender = 'Male' THEN 1 ELSE 0 END) as male_students,
                   SUM(CASE WHEN st.gender = 'Female' THEN 1 ELSE 0 END) as female_students
            FROM tbl_classes c
            LEFT JOIN tbl_students st ON st.class_id = c.class_id 
                 AND st.academic_year_id = ? 
                 AND st.is_deleted = 'n'
                 {$status_sql}
            WHERE c.status = 1 AND c.is_deleted = 'n'
              AND (c.academic_year_id = ? OR c.academic_year_id IS NULL OR c.academic_year_id = 0)
            GROUP BY c.class_id, c.class_name
            ORDER BY c.class_id ASC
        ", $params)->result();
    }

    /**
     * Get paginated and filtered students strictly matching academic year and filters.
     *
     * @param array $filters
     * @param int|null $limit
     * @param int|null $offset
     * @param string $order_col
     * @param string $order_dir
     * @return array
     */
    public function get_all_students_paginated($filters = array(), $limit = 10, $offset = 0, $order_col = 'st.student_id', $order_dir = 'ASC')
    {
        $this->db
            ->select("st.*, c.class_name, c.class_code, COALESCE(div.division_name, 'A') as division_name, COALESCE(div.division_name, 'A') as section_name, y.year_name")
            ->from('tbl_students st')
            ->join('tbl_classes c', 'c.class_id = st.class_id', 'left')
            ->join('tbl_divisions div', 'div.division_id = st.division_id', 'left')
            ->join('tbl_academic_years y', 'y.academic_year_id = st.academic_year_id', 'left')
            ->where('st.is_deleted', 'n');

        if (!empty($filters['academic_year_id'])) {
            $this->db->where('st.academic_year_id', (int)$filters['academic_year_id']);
        }
        if (!empty($filters['class_id'])) {
            $this->db->where('st.class_id', (int)$filters['class_id']);
        }
        if (!empty($filters['division_id'])) {
            $default_sec_id = $this->Division_model->get_default_division_id(!empty($filters['class_id']) ? (int)$filters['class_id'] : null);
            $this->db->group_start()
                ->where('st.division_id', (int)$filters['division_id']);
            if ((int)$filters['division_id'] === (int)$default_sec_id) {
                $this->db->or_where('st.division_id IS NULL', null, false)
                         ->or_where('st.division_id', 0);
            }
            $this->db->group_end();
        }
        if (!empty($filters['gender'])) {
            $this->db->where('st.gender', $filters['gender']);
        }
        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== 'All') {
            $this->db->where('st.status', (int)$filters['status']);
        }
        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $this->db->group_start()
                ->like('st.first_name', $s)
                ->or_like('st.last_name', $s)
                ->or_like('st.admission_number', $s)
                ->or_like('st.guardian_name', $s)
                ->or_like('st.guardian_phone', $s)
                ->or_like('st.roll_number', $s)
                ->group_end();
        }

        $this->db->order_by($order_col, $order_dir);

        if ($limit !== NULL && $limit > 0) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get()->result();
    }

    /**
     * Count total filtered students matching academic year and filters.
     *
     * @param array $filters
     * @return int
     */
    public function count_all_students($filters = array())
    {
        $this->db
            ->from('tbl_students st')
            ->where('st.is_deleted', 'n');

        if (!empty($filters['academic_year_id'])) {
            $this->db->where('st.academic_year_id', (int)$filters['academic_year_id']);
        }
        if (!empty($filters['class_id'])) {
            $this->db->where('st.class_id', (int)$filters['class_id']);
        }
        if (!empty($filters['division_id'])) {
            $default_sec_id = $this->Division_model->get_default_division_id(!empty($filters['class_id']) ? (int)$filters['class_id'] : null);
            $this->db->group_start()
                ->where('st.division_id', (int)$filters['division_id']);
            if ((int)$filters['division_id'] === (int)$default_sec_id) {
                $this->db->or_where('st.division_id IS NULL', null, false)
                         ->or_where('st.division_id', 0);
            }
            $this->db->group_end();
        }
        if (!empty($filters['gender'])) {
            $this->db->where('st.gender', $filters['gender']);
        }
        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== 'All') {
            $this->db->where('st.status', (int)$filters['status']);
        }
        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $this->db->group_start()
                ->like('st.first_name', $s)
                ->or_like('st.last_name', $s)
                ->or_like('st.admission_number', $s)
                ->or_like('st.guardian_name', $s)
                ->or_like('st.guardian_phone', $s)
                ->or_like('st.roll_number', $s)
                ->group_end();
        }

        return $this->db->count_all_results();
    }

    /* =========================================================================
       Bulk Student Management
       ========================================================================= */

    /**
     * Check if an admission number already exists.
     *
     * @param string $adm_no
     * @param int|null $exclude_student_id
     * @return bool
     */
    public function is_admission_number_exists($adm_no, $exclude_student_id = NULL)
    {
        $adm_no = trim($adm_no);
        if (empty($adm_no)) return false;

        $this->db->where('admission_number', $adm_no)
                 ->where('is_deleted', 'n');
        if ($exclude_student_id) {
            $this->db->where('student_id !=', (int)$exclude_student_id);
        }
        return ($this->db->count_all_results('tbl_students') > 0);
    }

    /**
     * Generate next sequential unique admission number.
     *
     * @param int|null $year_id
     * @return string e.g. EDU2026035
     */
    public function generate_unique_admission_number($year_id = NULL)
    {
        $yearStr = date('Y');
        if ($year_id) {
            $yRow = $this->db->select('year_name')->where('academic_year_id', (int)$year_id)->get('tbl_academic_years')->row();
            if ($yRow && preg_match('/(\d{4})/', $yRow->year_name, $m)) {
                $yearStr = $m[1];
            }
        }

        $prefix = 'EDU' . $yearStr;
        
        // Find highest existing suffix
        $highest = $this->db->query("
            SELECT admission_number 
            FROM tbl_students 
            WHERE admission_number LIKE ? 
            ORDER BY student_id DESC 
            LIMIT 50
        ", [$prefix . '%'])->result();

        $maxSeq = 0;
        foreach ($highest as $row) {
            if (preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/', $row->admission_number, $m)) {
                $seq = (int)$m[1];
                if ($seq > $maxSeq) $maxSeq = $seq;
            }
        }

        do {
            $maxSeq++;
            $candidate = $prefix . sprintf('%03d', $maxSeq);
        } while ($this->is_admission_number_exists($candidate));

        return $candidate;
    }

    /**
     * Parse and validate a batch of student records.
     *
     * @param array $raw_rows Array of associative student rows
     * @param int $academic_year_id
     * @param int $class_id
     * @param int $section_id
     * @return array
     */
    public function bulk_validate_students($raw_rows, $academic_year_id, $class_id, $section_id)
    {
        $validated = array();
        $seen_admissions_in_file = array();
        $valid_count = 0;
        $error_count = 0;

        foreach ($raw_rows as $idx => $row) {
            $row_num = $idx + 1;
            $errors = array();

            // 1. First Name (Required)
            $first_name = isset($row['first_name']) ? trim($row['first_name']) : '';
            if (empty($first_name)) {
                $errors[] = 'First Name is required.';
            }

            $middle_name = isset($row['middle_name']) ? trim($row['middle_name']) : '';
            $last_name = isset($row['last_name']) ? trim($row['last_name']) : '';

            // 2. Date of Birth (Required + Valid date)
            $raw_dob = isset($row['date_of_birth']) ? trim($row['date_of_birth']) : '';
            $dob = null;
            if (empty($raw_dob)) {
                $errors[] = 'Date of Birth is required.';
            } else {
                $raw_dob_clean = str_replace('/', '-', $raw_dob);
                $ts = strtotime($raw_dob_clean);
                if (!$ts || $ts > time() || $ts < strtotime('-40 years')) {
                    $errors[] = 'Invalid Date of Birth ("' . html_escape($raw_dob) . '").';
                } else {
                    $dob = date('Y-m-d', $ts);
                }
            }

            // 3. Gender (Required: Male, Female, Other)
            $raw_gender = isset($row['gender']) ? trim($row['gender']) : 'Male';
            $gender = 'Male';
            if (strcasecmp($raw_gender, 'female') === 0 || strcasecmp($raw_gender, 'f') === 0) {
                $gender = 'Female';
            } elseif (strcasecmp($raw_gender, 'other') === 0 || strcasecmp($raw_gender, 'o') === 0) {
                $gender = 'Other';
            } elseif (strcasecmp($raw_gender, 'male') === 0 || strcasecmp($raw_gender, 'm') === 0 || empty($raw_gender)) {
                $gender = 'Male';
            } else {
                $errors[] = 'Invalid Gender ("' . html_escape($raw_gender) . '"). Must be Male, Female, or Other.';
            }

            // 4. Blood Group (Optional)
            $raw_bg = isset($row['blood_group']) ? strtoupper(trim($row['blood_group'])) : '';
            $valid_bgs = array('A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-');
            $blood_group = in_array($raw_bg, $valid_bgs, true) ? $raw_bg : '';

            // 5. Guardian Name (Required)
            $guardian_name = isset($row['guardian_name']) ? trim($row['guardian_name']) : '';
            if (empty($guardian_name)) {
                $guardian_name = !empty($last_name) ? ('Parent of ' . $first_name) : 'Parent / Guardian';
            }

            // 6. Guardian Relation
            $guardian_relation = isset($row['guardian_relation']) ? trim($row['guardian_relation']) : 'Father';
            if (empty($guardian_relation)) $guardian_relation = 'Father';

            // 7. Parent/Guardian Contact Number (Mandatory)
            $guardian_phone = isset($row['guardian_phone']) ? trim((string)$row['guardian_phone']) : '';
            if ($guardian_phone === '' || $guardian_phone === '—' || $guardian_phone === '-') {
                $errors[] = 'Parent/Guardian Contact Number is required.';
                $guardian_phone = '';
            }

            // 8. Guardian Email
            $guardian_email = isset($row['guardian_email']) ? trim($row['guardian_email']) : '';
            if (!empty($guardian_email) && !filter_var($guardian_email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid Email address ("' . html_escape($guardian_email) . '").';
            }

            // 9. Address
            $address = isset($row['address']) ? trim($row['address']) : '';

            // 10. Roll Number
            $roll_number = isset($row['roll_number']) ? trim($row['roll_number']) : '';

            // 11. Admission Number (If provided, check uniqueness in DB and in file)
            $raw_adm = isset($row['admission_number']) ? trim($row['admission_number']) : '';
            $adm_number = $raw_adm;
            if (!empty($adm_number)) {
                $adm_key = strtolower($adm_number);
                if (isset($seen_admissions_in_file[$adm_key])) {
                    $errors[] = 'Duplicate Admission Number in batch (used in row ' . $seen_admissions_in_file[$adm_key] . ').';
                } else {
                    $seen_admissions_in_file[$adm_key] = $row_num;
                }

                if ($this->is_admission_number_exists($adm_number)) {
                    $errors[] = 'Admission Number "' . html_escape($adm_number) . '" already exists in database.';
                }
            } else {
                $adm_number = '(Auto-generate)';
            }

            $is_valid = empty($errors);
            if ($is_valid) {
                $valid_count++;
            } else {
                $error_count++;
            }

            $validated[] = array(
                'row_num'           => $row_num,
                'first_name'        => $first_name,
                'middle_name'       => $middle_name,
                'last_name'         => $last_name,
                'full_name'         => trim($first_name . ' ' . ($middle_name ? $middle_name . ' ' : '') . $last_name),
                'date_of_birth'     => $dob ?: $raw_dob,
                'gender'            => $gender,
                'blood_group'       => $blood_group,
                'guardian_name'     => $guardian_name,
                'guardian_relation' => $guardian_relation,
                'guardian_phone'    => $guardian_phone,
                'guardian_email'    => $guardian_email,
                'address'           => $address,
                'roll_number'       => $roll_number,
                'admission_number'  => $adm_number,
                'is_valid'          => $is_valid,
                'status'            => $is_valid ? 'Valid' : 'Error',
                'errors'            => $errors
            );
        }

        return array(
            'total_count' => count($raw_rows),
            'valid_count' => $valid_count,
            'error_count' => $error_count,
            'rows'        => $validated
        );
    }

    /**
     * Insert validated student rows in a transaction.
     *
     * @param array $valid_rows
     * @param int $academic_year_id
     * @param int $class_id
     * @param int $section_id
     * @return array Result summary ['success' => bool, 'inserted_count' => int, 'student_ids' => array, 'message' => string]
     */
    public function bulk_insert_students($valid_rows, $academic_year_id, $class_id, $section_id)
    {
        if (empty($valid_rows)) {
            return array('success' => false, 'inserted_count' => 0, 'message' => 'No valid student records to insert.');
        }

        $this->db->trans_start();

        $inserted_ids = array();
        $inserted_count = 0;
        $now = date('Y-m-d H:i:s');

        // Resolve default section ID if needed
        if (empty($section_id)) {
            $section_id = $this->Division_model->get_default_division_id($class_id);
        }

        foreach ($valid_rows as $row) {
            $first_name = isset($row['first_name']) ? trim($row['first_name']) : '';
            $guardian_phone = isset($row['guardian_phone']) ? trim((string)$row['guardian_phone']) : '';

            // Mandatory validation check before database insertion
            if (empty($first_name) || $guardian_phone === '' || $guardian_phone === '—' || $guardian_phone === '-') {
                continue;
            }

            // Generate unique admission number if needed
            $adm = isset($row['admission_number']) ? trim($row['admission_number']) : '';
            if (empty($adm) || $adm === '(Auto-generate)') {
                $adm = $this->generate_unique_admission_number($academic_year_id);
            }

            $student_data = array(
                'admission_number'  => $adm,
                'first_name'        => $first_name,
                'middle_name'       => !empty($row['middle_name']) ? $row['middle_name'] : NULL,
                'last_name'         => !empty($row['last_name']) ? $row['last_name'] : '',
                'gender'            => !empty($row['gender']) ? $row['gender'] : 'Male',
                'date_of_birth'     => !empty($row['date_of_birth']) ? $row['date_of_birth'] : date('Y-m-d'),
                'blood_group'       => !empty($row['blood_group']) ? $row['blood_group'] : '',
                'guardian_name'     => !empty($row['guardian_name']) ? $row['guardian_name'] : 'Parent',
                'guardian_relation' => !empty($row['guardian_relation']) ? $row['guardian_relation'] : 'Father',
                'guardian_phone'    => $guardian_phone,
                'guardian_email'    => !empty($row['guardian_email']) ? $row['guardian_email'] : '',
                'address'           => !empty($row['address']) ? $row['address'] : '',
                'roll_number'       => !empty($row['roll_number']) ? $row['roll_number'] : '',
                'academic_year_id'  => (int)$academic_year_id,
                'class_id'          => (int)$class_id,
                'division_id'        => (int)$section_id,
                'status'            => 1,
                'is_deleted'        => 'n',
                'created_at'        => $now,
                'updated_at'        => $now
            );

            $this->db->insert('tbl_students', $student_data);
            $new_id = $this->db->insert_id();
            if ($new_id) {
                $inserted_ids[] = $new_id;
                $inserted_count++;
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return array(
                'success'        => false,
                'inserted_count' => 0,
                'student_ids'    => array(),
                'message'        => 'Database transaction failed while adding students.'
            );
        }

        return array(
            'success'        => true,
            'inserted_count' => $inserted_count,
            'student_ids'    => $inserted_ids,
            'message'        => $inserted_count . ' students added successfully.'
        );
    }

    public function update_photo($student_id, $photo_filename)
    {
        return $this->db
            ->where($this->primaryKey, (int)$student_id)
            ->update($this->table, array('photo' => $photo_filename, 'updated_at' => date('Y-m-d H:i:s')));
    }

    public function delete_photo($student_id)
    {
        $student = $this->get_by_id($student_id);
        if ($student && !empty($student->photo)) {
            $filePath = FCPATH . 'uploads/students/' . $student->photo;
            if (file_exists($filePath) && is_file($filePath)) {
                @unlink($filePath);
            }
        }
        return $this->db
            ->where($this->primaryKey, (int)$student_id)
            ->update($this->table, array('photo' => NULL, 'updated_at' => date('Y-m-d H:i:s')));
    }
}



