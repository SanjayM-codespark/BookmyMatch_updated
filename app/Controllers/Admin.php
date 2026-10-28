<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\CommonModel;
use App\Models\TournamentModel;
use App\Libraries\RestLibrary;
use App\Libraries\NotificationLibrary;
use DB;
use Pusher\PushNotifications\PushNotifications;

class Admin extends Controller
{
	protected $rest;
	protected $common;
	protected $tournament;
	protected $notification;
	public function __construct()
	{
		helper(['form', 'url', 'custom']);
		$this->rest = new RestLibrary();
		$this->common = new CommonModel();
		$this->tournament = new TournamentModel();
		$this->notification = new NotificationLibrary();
	}

public function index()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['page_title']    = 'Dashboard';
		$data['page_subtitle'] = 'Welcome back, ' . (session()->get('username') ?? 'Admin');
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['tournaments'] = $this->tournament->get_tournament();
		$condition1 = array('user_role_id' => 3, 'user_active' => 0);

		$condition2 = array('user_role_id' => 3, 'user_active' => 1);

		$data['data1'] = $this->common->select1('users', '*', $condition1);
		$data['data2'] = $this->common->select1('users', '*', $condition2);
		$db = \Config\Database::connect();
		$builder = $db->table('users u');
		$builder->where('u.user_role_id', 2);
		$data['unique'] = $builder->countAllResults();

		$builder = $db->table('users');
        $builder->selectSum('login_count');
        $query = $builder->get();
        $row = $query->getRow();
        $data['total_visit'] = $row->login_count ?? 0;

		//print_array($data['tournaments']);die();
		echo view('admin/home', $data);
	}


	/*---------------------------- banners -----------------------*/
	public function home_banners()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url('admin'));
		}
		$data['page_name'] = 'Home Banners';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['home_banner'] = $this->common->select('home_banner', '*', array('home_banner_active' => 1), true);
		$data['commonModel'] = $this->common;
		echo view('admin/home_banners', $data);
	}

	public function add_home_banner()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url('admin'));
		}
		$data['page_name'] = 'Add Home Banner';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));


		echo view('admin/add_home_banner', $data);
	}

	public function edit_home_banner($id)
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url('admin'));
		}
		$data['page_name'] = 'Edit Home Banner';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));


		$data['home_banner'] = $this->common->select('home_banner', '*', array('home_banner_active' => 1, 'home_banner_id' => $id));
		echo view('admin/add_home_banner', $data);
	}

	public function attempt_home_banner()
	{

		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url('admin'));
		}
		$rules = [
			'top_image' => ['label' => 'Upload Image', 'rules' => 'required'],
			'top_image1' => ['label' => 'Upload Website Image', 'rules' => 'required']
		];

		if (! $this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}
		$home_banner_id = !empty($_POST['home_banner_id']) ? xss_clean($_POST['home_banner_id']) : NULL;

		$top_image = !empty($_POST['top_image']) ? xss_clean($_POST['top_image']) : NULL;
		$top_image1 = !empty($_POST['top_image1']) ? xss_clean($_POST['top_image1']) : NULL;
		$data = [
			'home_banner_image' => $top_image,
			'home_banner_web_image' => $top_image1
		];

		if (!empty($home_banner_id)) {
			$data['home_banner_updated_at'] = date('Y-m-d H:i:s');
			$result = $this->common->update_data('home_banner', $data, array('home_banner_id' => $home_banner_id));
			$msg = 'Home Banner updated successfully!';
		} else {
			$data['home_banner_created_at'] = date('Y-m-d H:i:s');
			$result = $this->common->insert_data('home_banner', $data);
			$msg = 'Home Banner added successfully!';
		}

		if ($result) {
			return redirect()->to(base_url('admin/home_banners'))->with('success_message', $msg);
		} else {
			return redirect()->back()->withInput()->with('error_message', 'Something went wrong. Try again');
		}
	}

	public function delete_home_banner()
	{
		$data = [];
		$home_banner_id = $this->request->getPost('home_banner_id');
		if (!empty($home_banner_id)) {
			try {
				$this->common->delete_data('home_banner', array('home_banner_id' => $home_banner_id));
				$data['success'] = 'success';
				$data['message'] = 'Home banner deleted successfully!';
			} catch (\Exception $e) {
				$data['error'] = 'fail';
				$data['message'] = 'Something went wrong. Try again.';
			}
		} else {
			$data['error'] = 'fail';
			$data['message'] = 'Home banner is required';
		}

		echo json_encode($data);
		exit;
	}

	/*---------------------------- End banners -----------------------*/

	public function sports()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['sports'] = $this->common->select('sports', '*', array('sports_active' => 1), true);
		//print_array($data['sports']);die();
		echo view('master_entries/sports', $data);
	}

	public function edit_sports($id)
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['sports'] = $this->common->select('sports', '*', array('sports_active' => 1), true);
		$data['sport'] = $this->common->select('sports', '*', array('sports_id' => $id));
		$data['edit'] = 'yes';
		//print_array($data['sports']);die();
		echo view('master_entries/sports', $data);
	}

	public function attempt_add_sports($id = null)
	{
		//echo '<pre>';print_r($this->request);die();
		$rules = [
			'name'	=>  ['label' => 'Sports Name', 'rules' => 'required|min_length[3]']
		];


		if (! $this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}

		$mfr_name = trim($this->request->getPost('name'));
		$condition = array('sports_name' => $mfr_name);
		if (!empty($id)) {
			$condition = array('sports_name' => $mfr_name, 'sports_id !=' => $id);
		}
		$sports_exist = $this->common->select('sports', '*', $condition);
		if (!empty($sports_exist)) {
			return redirect()->back()->withInput()->with('error', 'Sports already exist');
		}
		$max_players = (int)$this->request->getPost('max_players');
		$data = [
			'sports_name'        => xss_clean($mfr_name),
			'sports_max_players' => $max_players > 0 ? $max_players : 1,
		];

		if (!empty($this->request->getFile('image'))) {
			$folderpath = FCPATH . 'public/uploads/images/sports';
			$imageFile = $this->request->getFile('image');
			if ($imageFile->isValid() && ! $imageFile->hasMoved()) {
				$filename = $imageFile->getRandomName();
				$imageFile->move($folderpath, $filename);
				$data['sports_image'] = $filename;
			}
		}

		if (!empty($id)) {
			$result = $this->common->update_data('sports', $data, array('sports_id' => $id));
		} else {
			$result = $this->common->insert_data('sports', $data);
		}
		if ($result) {
			if (!empty($id)) {
				$msg = 'Sports updated successfully!';
			} else {
				$msg = 'Sports added successfully!';
			}
			return redirect()->to(base_url('admin/sports'))->with('success_message', $msg);
		} else {
			return redirect()->back()->withInput()->with('error', 'Something went wrong. please try again.');
		}
	}

	// ─────────────────────────────────────────────────────────────
	// PACKAGES
	// ─────────────────────────────────────────────────────────────

	public function packages()
	{
		if (session()->get('role_id') != 1) return redirect()->to(base_url());
		$data['body_class'] = 'skin-default card-no-border';
		$data['users']    = $this->common->select('users', '*', ['user_id' => session()->get('user_id')]);
		$data['packages'] = $this->common->select('packages', '*', ['package_active !=' => -1], true);
		echo view('admin/packages', $data);
	}

	public function edit_package($id)
	{
		if (session()->get('role_id') != 1) return redirect()->to(base_url());
		$data['body_class'] = 'skin-default card-no-border';
		$data['users']    = $this->common->select('users', '*', ['user_id' => session()->get('user_id')]);
		$data['packages'] = $this->common->select('packages', '*', ['package_active !=' => -1], true);
		$data['package']  = $this->common->select('packages', '*', ['package_id' => $id]);
		$data['edit']     = 'yes';
		echo view('admin/packages', $data);
	}

	public function attempt_add_package($id = null)
	{
		if (session()->get('role_id') != 1) return redirect()->to(base_url());

		$rules = [
			'package_name'  => ['label' => 'Package Name', 'rules' => 'required|min_length[2]'],
			'package_price' => ['label' => 'Price',        'rules' => 'required|numeric'],
		];
		if (!$this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}

		$durationType = $this->request->getPost('package_duration_type');
		$durationDays = match($durationType) {
			'monthly' => 30,
			'6month' => 180,
			'yearly' => 365,
			default => 30,
		};

		$data = [
			'package_name'                  => xss_clean($this->request->getPost('package_name')),
			'package_description'           => xss_clean($this->request->getPost('package_description')),
			'package_duration_type'         => $durationType,
			'package_duration_days'         => $durationDays,
			'package_price'                 => (float) $this->request->getPost('package_price'),
			'package_max_matches'           => (int)   ($this->request->getPost('package_max_matches') ?? 0),
			'package_max_tournaments'       => (int)   ($this->request->getPost('package_max_tournaments') ?? 0),
			'package_max_teams'             => (int)   ($this->request->getPost('package_max_teams') ?? 0),
			'package_can_create_tournament' => !empty($this->request->getPost('package_can_create_tournament')) ? 1 : 0,
			'package_priority_booking'      => !empty($this->request->getPost('package_priority_booking')) ? 1 : 0,
			'package_is_featured'           => !empty($this->request->getPost('package_is_featured')) ? 1 : 0,
			'package_sort_order'            => (int)   ($this->request->getPost('package_sort_order') ?? 0),
		];

		if ($id) {
			$data['package_updated_at'] = date('Y-m-d H:i:s');
			$result = $this->common->update_data('packages', $data, ['package_id' => $id]);
			$msg = 'Package updated successfully!';
		} else {
			$data['package_active']     = 1;
			$data['package_created_at'] = date('Y-m-d H:i:s');
			$result = $this->common->insert_data('packages', $data);
			$msg = 'Package added successfully!';
		}

		if ($result) {
			return redirect()->to(base_url('admin/packages'))->with('success_message', $msg);
		}
		return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
	}

	// ─────────────────────────────────────────────────────────────
	// MEMBERSHIPS
	// ─────────────────────────────────────────────────────────────

// 	public function memberships()
// 	{
// 		if (session()->get('role_id') != 1) return redirect()->to(base_url());
// 		$data['body_class'] = 'skin-default card-no-border';
// 		$data['users']    = $this->common->select('users', '*', ['user_id' => session()->get('user_id')]);
// 		$data['packages'] = $this->common->select('packages', '*', ['package_active' => 1], true);

// 		$db = \Config\Database::connect();

// 		// ── Filters ──
// 		$builder = $db->table('memberships m')
// 			->select('m.*, u.user_full_name, u.user_mobile, u.user_email, p.package_name, p.package_duration_type, p.package_price')
// 			->join('users u', 'u.user_id = m.membership_user_id', 'left')
// 			->join('packages p', 'p.package_id = m.membership_package_id', 'left')
// 			->orderBy('m.membership_created_at', 'DESC');

// 		if (!empty($_GET['package_id'])) $builder->where('m.membership_package_id', (int) $_GET['package_id']);
// 		if (!empty($_GET['status']))     $builder->where('m.membership_status', $_GET['status']);
// 		if (!empty($_GET['from']))       $builder->where('m.membership_created_at >=', $_GET['from'] . ' 00:00:00');
// 		if (!empty($_GET['to']))         $builder->where('m.membership_created_at <=', $_GET['to']   . ' 23:59:59');

// 		$data['memberships'] = $builder->get()->getResult();

// 		// ── Overall stats ──
// 		$data['stats'] = $db->query("
// 			SELECT
// 				COUNT(*)                                      AS total,
// 				SUM(membership_status = 'active')             AS active,
// 				SUM(membership_status = 'expired')            AS expired,
// 				SUM(membership_status = 'cancelled')          AS cancelled,
// 				SUM(membership_price_paid)                    AS total_revenue,
// 				SUM(membership_status = 'active' AND membership_price_paid > 0) AS paid_active
// 			FROM memberships
// 		")->getRow();

// 		// ── Revenue per package ──
// 		$data['package_revenue'] = $db->query("
// 			SELECT p.package_name, p.package_duration_type,
// 				COUNT(m.membership_id)           AS total_buyers,
// 				SUM(m.membership_status='active') AS active_count,
// 				SUM(m.membership_price_paid)      AS revenue
// 			FROM memberships m
// 			JOIN packages p ON p.package_id = m.membership_package_id
// 			GROUP BY m.membership_package_id
// 			ORDER BY revenue DESC
// 		")->getResult();

// 		// ── Last 6 months earnings ──
// 		$data['monthly_revenue'] = $db->query("
// 			SELECT
// 				DATE_FORMAT(membership_created_at, '%b %Y') AS month_label,
// 				DATE_FORMAT(membership_created_at, '%Y-%m') AS month_sort,
// 				COUNT(*)                                    AS purchases,
// 				SUM(membership_price_paid)                  AS revenue
// 			FROM memberships
// 			WHERE membership_created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
// 			GROUP BY month_sort
// 			ORDER BY month_sort ASC
// 		")->getResult();

// 		echo view('admin/memberships', $data);
// 	}


public function memberships()
{
    if (session()->get('role_id') != 1) return redirect()->to(base_url());
 
    $data['body_class'] = 'skin-default card-no-border';
    $data['users']    = $this->common->select('users', '*', ['user_id' => session()->get('user_id')]);
    $data['packages'] = $this->common->select('packages', '*', ['package_active' => 1], true);
 
    $filters = $this->getMembershipFilters();
    $data['filters'] = $filters;
 
    $db = \Config\Database::connect();
    $data['memberships'] = $this->buildMembershipQuery($db, $filters)->get()->getResult();
 
    // ── Overall stats ──
    $data['stats'] = $db->query("
        SELECT
            COUNT(*)                                                        AS total,
            SUM(membership_status = 'active')                               AS active,
            SUM(membership_status = 'expired')                              AS expired,
            SUM(membership_status = 'cancelled')                            AS cancelled,
            SUM(membership_price_paid)                                      AS total_revenue,
            SUM(membership_status = 'active' AND membership_price_paid > 0) AS paid_active
        FROM memberships
    ")->getRow();
 
    // ── Renewal vs new stats (computed, no schema change) ──
    // A row counts as a "renewal" if the same user bought the same package
    // at an earlier date, i.e. an earlier memberships row exists for that
    // user + package pair.
    $data['renewal_stats'] = $db->query("
        SELECT
            SUM(is_renewal = 1) AS renewals,
            SUM(is_renewal = 0) AS new_purchases
        FROM (
            SELECT m.membership_id,
                EXISTS (
                    SELECT 1 FROM memberships m2
                    WHERE m2.membership_user_id = m.membership_user_id
                      AND m2.membership_package_id = m.membership_package_id
                      AND m2.membership_created_at < m.membership_created_at
                ) AS is_renewal
            FROM memberships m
        ) t
    ")->getRow();
 
    // ── Revenue per package ──
    $data['package_revenue'] = $db->query("
        SELECT p.package_name, p.package_duration_type,
            COUNT(m.membership_id)            AS total_buyers,
            SUM(m.membership_status='active')  AS active_count,
            SUM(m.membership_price_paid)       AS revenue
        FROM memberships m
        JOIN packages p ON p.package_id = m.membership_package_id
        GROUP BY m.membership_package_id
        ORDER BY revenue DESC
    ")->getResult();
 
    // ── Last 6 months earnings ──
    $data['monthly_revenue'] = $db->query("
        SELECT
            DATE_FORMAT(membership_created_at, '%b %Y') AS month_label,
            DATE_FORMAT(membership_created_at, '%Y-%m') AS month_sort,
            COUNT(*)                                    AS purchases,
            SUM(membership_price_paid)                  AS revenue
        FROM memberships
        WHERE membership_created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY month_sort
        ORDER BY month_sort ASC
    ")->getResult();
 
    echo view('admin/memberships', $data);
}
 
/**
 * Reads & sanitizes filter inputs from the request (not raw $_GET).
 * Shared by the listing page and the export endpoint, so "export
 * filtered" always matches exactly what's shown on screen.
 */
private function getMembershipFilters()
{
    $get = $this->request->getGet();
 
    $filters = [
        'package_id' => !empty($get['package_id']) ? (int) $get['package_id'] : null,
        'status'     => !empty($get['status']) ? trim($get['status']) : null,
        'type'       => !empty($get['type']) ? trim($get['type']) : null, // 'new' | 'renewal'
        'from'       => !empty($get['from']) ? trim($get['from']) : null,
        'to'         => !empty($get['to']) ? trim($get['to']) : null,
    ];
 
    // Guard against inverted date ranges silently returning zero rows
    if (!empty($filters['from']) && !empty($filters['to']) && $filters['from'] > $filters['to']) {
        [$filters['from'], $filters['to']] = [$filters['to'], $filters['from']];
    }
 
    // Only allow known values through
    if ($filters['status'] !== null && !in_array($filters['status'], ['active', 'expired', 'cancelled'], true)) {
        $filters['status'] = null;
    }
    if ($filters['type'] !== null && !in_array($filters['type'], ['new', 'renewal'], true)) {
        $filters['type'] = null;
    }
 
    return $filters;
}
 
/**
 * Shared query builder used by both the listing page and export.
 * Adds an inline "is_renewal" flag via correlated subquery — no
 * schema change required.
 */
private function buildMembershipQuery($db, array $filters)
{
    $builder = $db->table('memberships m')
        ->select("m.*, u.user_full_name, u.user_mobile, u.user_email,
                  p.package_name, p.package_duration_type, p.package_price,
                  EXISTS (
                      SELECT 1 FROM memberships m2
                      WHERE m2.membership_user_id = m.membership_user_id
                        AND m2.membership_package_id = m.membership_package_id
                        AND m2.membership_created_at < m.membership_created_at
                  ) AS is_renewal", false)
        ->join('users u', 'u.user_id = m.membership_user_id', 'left')
        ->join('packages p', 'p.package_id = m.membership_package_id', 'left')
        ->orderBy('m.membership_created_at', 'DESC');
 
    if ($filters['package_id']) {
        $builder->where('m.membership_package_id', $filters['package_id']);
    }
    if ($filters['status']) {
        $builder->where('m.membership_status', $filters['status']);
    }
    if ($filters['from']) {
        $builder->where('m.membership_created_at >=', $filters['from'] . ' 00:00:00');
    }
    if ($filters['to']) {
        $builder->where('m.membership_created_at <=', $filters['to'] . ' 23:59:59');
    }
    if ($filters['type'] === 'renewal') {
        $builder->having('is_renewal', 1);
    } elseif ($filters['type'] === 'new') {
        $builder->having('is_renewal', 0);
    }
 
    return $builder;
}
 
/**
 * CSV export — opens directly in Excel, no external library required.
 * GET admin/memberships/export       -> respects current filters (querystring)
 * GET admin/memberships/export/all   -> ignores filters, exports everything
 */
public function export_memberships($mode = null)
{
    if (session()->get('role_id') != 1) return redirect()->to(base_url());
 
    $all = ($mode === 'all');
    $db  = \Config\Database::connect();
 
    $filters = $all
        ? ['package_id' => null, 'status' => null, 'type' => null, 'from' => null, 'to' => null]
        : $this->getMembershipFilters();
 
    $rows = $this->buildMembershipQuery($db, $filters)->get()->getResult();
 
    $filename = 'memberships_' . ($all ? 'all_' : 'filtered_') . date('Ymd_His') . '.csv';
 
    if (ob_get_length()) {
        ob_end_clean();
    }
 
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
 
    $out = fopen('php://output', 'w');
 
    // BOM so Excel reads UTF-8 special characters (₹, accented names, etc.) correctly
    fputs($out, "\xEF\xBB\xBF");
 
    fputcsv($out, ['#', 'User', 'Mobile', 'Email', 'Package', 'Duration', 'Type', 'Price Paid', 'Start', 'Expires', 'Payment Ref', 'Purchased On', 'Status']);
 
    $durationLabels = ['monthly' => 'Monthly', '6month' => '6 Months', 'yearly' => 'Yearly'];
 
    foreach ($rows as $i => $m) {
        fputcsv($out, [
            $i + 1,
            $m->user_full_name ?? '—',
            $m->user_mobile ?? '—',
            $m->user_email ?? '—',
            $m->package_name ?? '—',
            $durationLabels[$m->package_duration_type] ?? ucfirst($m->package_duration_type ?? ''),
            !empty($m->is_renewal) ? 'Renewal' : 'New',
            $m->membership_price_paid == 0 ? 'Free' : number_format((float) $m->membership_price_paid, 2),
            !empty($m->membership_start_date) ? date('d M Y', strtotime($m->membership_start_date)) : '—',
            !empty($m->membership_end_date) ? date('d M Y', strtotime($m->membership_end_date)) : '—',
            $m->membership_payment_ref ?? '—',
            !empty($m->membership_created_at) ? date('d M Y H:i', strtotime($m->membership_created_at)) : '—',
            ucfirst($m->membership_status ?? ''),
        ]);
    }
 
    fclose($out);
    exit;
}

	public function country()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['countries'] = $this->common->select('countries', '*', array('country_active' => 1), true);
		//print_array($data['sports']);die();
		echo view('master_entries/country', $data);
	}

	public function edit_country($id)
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['countries'] = $this->common->select('countries', '*', array('country_active' => 1), true);
		$data['country'] = $this->common->select('countries', '*', array('country_id' => $id));
		$data['edit'] = 'yes';
		//print_array($data['sports']);die();
		echo view('master_entries/country', $data);
	}

	public function attempt_add_country($id = null)
	{
		//echo '<pre>';print_r($this->request);die();
		$rules = [
			'name'	=>  ['label' => 'Country Name', 'rules' => 'required|min_length[3]|is_unique[countries.country_name]', 'errors' => ['is_unique' => 'Country already exist']],
			'code'	=>  ['label' => 'Country Mobile Code', 'rules' => 'required']
		];


		if (! $this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}

		$country_name = trim($this->request->getPost('name'));
		$mobile_code = trim($this->request->getPost('code'));

		$data = [
			'country_name' => xss_clean($country_name),
			'country_mobile_code' => xss_clean($mobile_code)
		];

		if (!empty($id)) {
			$result = $this->common->update_data('countries', $data, array('country_id' => $id));
		} else {
			$result = $this->common->insert_data('countries', $data);
		}
		if ($result) {
			if (!empty($id)) {
				$msg = 'Country updated successfully!';
			} else {
				$msg = 'Country added successfully!';
			}
			return redirect()->to(base_url('admin/country'))->with('success_message', $msg);
		} else {
			return redirect()->back()->withInput()->with('error', 'Something went wrong. please try again.');
		}
	}
	/*-------------------------------- states-------------------------------------*/

	public function states()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['countries'] = $this->common->select('countries', '*', array(), true);
		$data['states'] = $this->common->select('state', '*', array(), true);
		$data['commonModel'] = $this->common;
		//print_array($data['sports']);die();
		echo view('master_entries/states', $data);
	}

	public function edit_states($id)
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['countries'] = $this->common->select('countries', '*', array('country_active' => 1), true);
		$data['commonModel'] = $this->common;

		$data['state'] = $this->common->select('state', '*', array('state_id' => $id));
						$data['states'] = $this->common->select('state', '*', array(), true);
		$data['edit'] = 'yes';
		//print_array($data['sports']);die();
		echo view('master_entries/states', $data);
	}

	public function attempt_add_states($id = null)
	{
		//echo '<pre>';print_r($this->request);die();
		$rules = [
			'country'	=>  ['label' => 'Country', 'rules' => 'required'],
			'state'	=>  ['label' => 'State name', 'rules' => 'required|min_length[2]']
		];


		if (! $this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}
		$date = date('Y-m-d H:i:s');
		$city_name = trim($this->request->getPost('state'));
		$country = trim($this->request->getPost('country'));

		$data = [
			'state_name' => xss_clean($city_name),
			'state_country_id' => xss_clean($country)
		];

		$cond = array('state_country_id' => $country, 'state_name' => $city_name);
		if (!empty($id)) {
			$cond['state_id !='] = $id;
		}
		$city_exist = $this->common->select('state', '*', $cond);
		if (!empty($city_exist)) {
			return redirect()->back()->withInput()->with('error', 'State already exist');
		}

		if (!empty($id)) {
			$data['state_updated_at'] = $date;
			$result = $this->common->update_data('state', $data, array('state_id' => $id));
		} else {
			$data['state_created_at'] = $date;
			$result = $this->common->insert_data('state', $data);
		}
		if ($result) {
			if (!empty($id)) {
				$msg = 'State updated successfully!';
			} else {
				$msg = 'State added successfully!';
			}
			return redirect()->to(base_url('admin/states'))->with('success_message', $msg);
		} else {
			return redirect()->back()->withInput()->with('error', 'Something went wrong. please try again.');
		}
	}
	/*--------------------------------- cities -----------------------------------*/

	public function cities()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['countries'] = $this->common->select('countries', '*', array('country_active' => 1), true);
		$data['states'] = $this->common->select('state', '*', array('state_active' => 1), true);
		$data['commonModel'] = $this->common;

		  $page = $this->request->getVar('page') ?? 1;
    $perPage = 10;

    $pager = \Config\Services::pager();
    $offset = ($page - 1) * $perPage;
    		$data['offset'] = $offset;

        $data['cities'] = $this->common->get_limit_data('cities', '*', array('city_active' => 1), $perPage, $offset);
				   $total = $this->common->count_data('cities', array('city_active' => 1));

    $data['pager'] = $pager->makeLinks($page, $perPage, $total, 'default_full'); 
		//print_array($data['sports']);die();
		echo view('master_entries/cities', $data);
	}

	public function edit_cities($id)
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['countries'] = $this->common->select('countries', '*', array('country_active' => 1), true);
		$data['states'] = $this->common->select('state', '*', array('state_active' => 1), true);
		$data['commonModel'] = $this->common;
 $total = $this->common->count_data('cities', array('city_active' => 1));

		  $page = $this->request->getVar('page') ?? 1;
    $perPage = 10;

    $pager = \Config\Services::pager();
    $offset = ($page - 1) * $perPage;
        		$data['offset'] = $offset;

        $data['cities'] = $this->common->get_limit_data('cities', '*', array('city_active' => 1), $perPage, $offset);

    $data['pager'] = $pager->makeLinks($page, $perPage, $total, 'default_full'); 
		$data['city'] = $this->common->select('cities', '*', array('city_id' => $id));
		$data['edit'] = 'yes';
					$state_id = $data['city']->city_state_id ?? null;
		$data['districts'] = $this->common->select('districts', '*', ['district_state_id' => $state_id], true);
		echo view('master_entries/cities', $data);
	}


	public function attempt_add_cities($id = null)
	{
		//echo '<pre>';print_r($this->request);die();
		$rules = [
			'country'	=>  ['label' => 'Country', 'rules' => 'required'],
			'state'	=>  ['label' => 'Country', 'rules' => 'required'],
			'city'	=>  ['label' => 'City name', 'rules' => 'required|min_length[2]'],
					'district'	=>  ['rules' => 'required']

		];


		if (! $this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}
		$state_name = trim($this->request->getPost('state'));
		$city_name = trim($this->request->getPost('city'));
		$country = trim($this->request->getPost('country'));
						$district = trim($this->request->getPost('district'));

		$data = [
			'city_name' => xss_clean($city_name),
			'city_state_id' => xss_clean($state_name),
			'city_country_id' => xss_clean($country),
						'city_district_id' => xss_clean($district)
		];
		$cond = array('city_state_id' => $state_name, 'city_name' => $city_name);
		if (!empty($id)) {
			$cond['city_id !='] = $id;
		}
		$city_exist = $this->common->select('cities', '*', $cond);
		if (!empty($city_exist)) {
			return redirect()->back()->withInput()->with('error', 'City already exist');
		}

		if (!empty($id)) {
			$result = $this->common->update_data('cities', $data, array('city_id' => $id));
		} else {
			$result = $this->common->insert_data('cities', $data);
		}
		if ($result) {
			if (!empty($id)) {
				$msg = 'City updated successfully!';
			} else {
				$msg = 'City added successfully!';
			}
			return redirect()->to(base_url('admin/cities'))->with('success_message', $msg);
		} else {
			return redirect()->back()->withInput()->with('error', 'City already exist.');
		}
	}
	public function attempt_add_districts($id = null)
	{
		//echo '<pre>';print_r($this->request);die();
		$rules = [
			'country'	=>  ['label' => 'Country', 'rules' => 'required'],
			'state'	=>  ['label' => 'Country', 'rules' => 'required'],
			'district'	=>  ['label' => 'District name', 'rules' => 'required|min_length[2]']
		];


		if (! $this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}
		$state_name = trim($this->request->getPost('state'));
		$district_name = trim($this->request->getPost('district'));
		$country = trim($this->request->getPost('country'));

		$data = [
			'district_name' => xss_clean($district_name),
			'district_state_id' => xss_clean($state_name),
			'district_country_id' => xss_clean($country)
		];
		$cond = array('district_state_id' => $state_name, 'district_name' => $district_name);
		if (!empty($id)) {
			$cond['district_id !='] = $id;
		}
		$district_exist = $this->common->select('districts', '*', $cond);
		if (!empty($district_exist)) {
			return redirect()->back()->withInput()->with('error', 'District already exist');
		}

		if (!empty($id)) {
			$result = $this->common->update_data('districts', $data, array('district_id' => $id));
		} else {
			$result = $this->common->insert_data('districts', $data);
		}
		if ($result) {
			if (!empty($id)) {
				$msg = 'District updated successfully!';
			} else {
				$msg = 'District added successfully!';
			}
			return redirect()->to(base_url('admin/districts'))->with('success_message', $msg);
		} else {
			return redirect()->back()->withInput()->with('error', 'District already exist');
		}
	}


public function organizer_list($slug = NULL)
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		
		$condition = array('user_role_id' => 3);
		if (!empty($slug)) {
			if ($slug == 'new') {
				$condition = array('user_role_id' => 3, 'user_active' => 0);
			}
			if ($slug == 'verified') {
				$condition = array('user_role_id' => 3, 'user_active' => 1);
			}
		}
						$condition1 = array('user_role_id' => 3, 'user_active' => 0);

						$condition2 = array('user_role_id' => 3, 'user_active' => 1);

				$data['data1'] = $this->common->select1('users', '*', $condition1);
								$data['data2'] = $this->common->select1('users', '*', $condition2);


		$data['organizers'] = $this->common->select('users', '*', $condition, true, 'user_id DESC');
		echo view('admin/organizer_list', $data);
	}

    public function tournament_list($slug = NULL)
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
				$search = $this->request->getGET('search');

		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));

		$item_count = $this->tournament->count_admin_tournament_list($slug,$search);
		//echo $item_count;exit;
		$data['total'] = $item_count;
		$per_page = 10;
		$page = $this->request->getGET('page');
		$offset = ($page * $per_page) - $per_page;
		if (empty($page)) {
			$page = 1;
			$offset = 0;
		}
		$data['page'] = $page;
		$data['perPage'] = $per_page;
		$data['tournaments'] = $this->tournament->get_admin_tournament_list($per_page, $offset, $slug,$search);
		//print_array($data['tournaments']);die();
		echo view('admin/tournament_list', $data);
	}

	public function inactive_tournament()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url('admin'));
		}
		$data = [];
		$user_id = $this->request->getPost('id');
		if (!empty($user_id)) {
			try {

				$this->common->update_data('listings', array('listing_active' => 0), array('listing_id' => $user_id));
				$data['success'] = 'success';
				$data['message'] = 'Tournament inactive successfully!';
			} catch (\Exception $e) {
				$data['error'] = 'fail';
				$data['message'] = 'Something went wrong. Try again.';
			}
		} else {
			$data['error'] = 'fail';
			$data['message'] = 'Tournament id is required';
		}

		echo json_encode($data);
		exit;
	}

	public function active_tournament()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url('admin'));
		}
		$data = [];
		$user_id = $this->request->getPost('id');
		if (!empty($user_id)) {
			try {

				$this->common->update_data('listings', array('listing_active' => 1), array('listing_id' => $user_id));
				$data['success'] = 'success';
				$data['message'] = 'Tournament Activated successfully!';
			} catch (\Exception $e) {
				$data['error'] = 'fail';
				$data['message'] = 'Something went wrong. Try again.';
			}
		} else {
			$data['error'] = 'fail';
			$data['message'] = 'Tournament id is required';
		}

		echo json_encode($data);
		exit;
	}

    public function participants($list_id,$event_id)
	{
		if ((session()->get('role_id') != 1) && (session()->get('role_id') != 3)) {
			return redirect()->to(base_url());
		}
		$db = \Config\Database::connect();

		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		
		
		
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['commonModel'] = $this->common;
		$data['tourneyModel'] = $this->tournament;

		$listing_event = $db->table('listing_events')
			->where('listing_event_id', $event_id)
			->get()->getRow();
			
			

		$data['event'] = $db->table('listing_events')
			->select('events.event_name, listing_events.listing_event_fee, listing_events.listing_event_type, listing_events.listing_event_max_players')
			->join('events', 'events.event_id = listing_events.listing_event_title', 'left')
			->where('listing_events.listing_event_id', $event_id)
			->get()->getRow();

		$event_type = $listing_event->listing_event_type ?? 'individual';

		if ($event_type === 'team' || $event_type === 'doubles') {
		    
		    
		    
			$data['is_team_event'] = true;
			$data['participants']  = [];

			$teams = $db->table('teams')
				->where('team_listing_event_id', $event_id)
				->orderBy('team_id', 'ASC')
				->get()->getResult();
				
			foreach ($teams as $team) {
				$team->members = $db->table('team_members')
					->where('tm_team_id', $team->team_id)
					->where('tm_status', 'accepted')
					->get()->getResult();
			}
			$data['teams'] = $teams;
		} else {
			$data['is_team_event'] = false;
			$search = $this->request->getGET('search');
			$data['participants'] = $this->common->selected('participants', '*', array('participant_list_id' => $list_id, 'participant_event_id' => $event_id), true, '', $search);
			$data['teams'] = [];
		}

		$data['page_title']    = 'Participants';
		$data['page_subtitle'] = 'List of registered participants for this event';
		$data['list_id']       = $list_id;
		$data['event_id']      = $event_id;
		echo view('admin/participants_list', $data);
	}

public function events()
{
    $data['body_class'] = 'skin-default card-no-border';
    $user_id = session()->get('user_id');

    $db = \Config\Database::connect();

		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));

    // Pagination setup
    $page = $this->request->getGet('page'); // current page number from URL ?page=2 etc.
    if (!$page || $page < 1) {
        $page = 1;
    }
    $limit = 10;
    $offset = ($page - 1) * $limit;

    // Get total records count (for pagination)
    $totalEvents = $db->table('events')
        ->groupStart()
            ->where('event_user_id', $user_id)
            ->orWhere('event_user_id', 2)
        ->groupEnd()
        ->countAllResults(false); // false = do not reset query builder so we can reuse it

    // Get events with limit and offset
    $events = $db->table('events')
        ->groupStart()
            ->where('event_user_id', $user_id)
            ->orWhere('event_user_id', 2)
        ->groupEnd()
         ->orderBy('event_user_id', 'DESC')
        ->limit($limit, $offset)
        ->get()
        ->getResult();

    $data['events'] = $events;

    // Prepare pagination data (simple previous/next)
    $data['pagination'] = [
        'current_page' => $page,
        'total_pages' => ceil($totalEvents / $limit),
    ];

if ((session()->get('role_id') == 3)) {
    echo view('master_entries/events', $data);
		}else{
			    echo view('master_entries/events1', $data);

		}}

	public function edit_events($id)
{
    $data['body_class'] = 'skin-default card-no-border';
    $user_id = session()->get('user_id');

    $db = \Config\Database::connect();

    $data['users'] = $db->table('users')->where('user_id', $user_id)->get()->getRow();
    $data['event'] = $db->table('events')->where('event_id', $id)->get()->getRow();
    $data['edit'] = 'yes';

    // Pagination setup
    $page = $this->request->getGet('page');
    if (!$page || $page < 1) {
        $page = 1;
    }
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $totalEvents = $db->table('events')
        ->groupStart()
            ->where('event_user_id', $user_id)
            ->orWhere('event_user_id', 2)
        ->groupEnd()
        ->countAllResults(false);

    $events = $db->table('events')
        ->groupStart()
            ->where('event_user_id', $user_id)
            ->orWhere('event_user_id', 2)
        ->groupEnd()
        ->orderBy('event_user_id', 'DESC')
        ->limit($limit, $offset)
        ->get()
        ->getResult();

    $data['events'] = $events;

    $data['pagination'] = [
        'current_page' => $page,
        'total_pages' => ceil($totalEvents / $limit),
    ];

    // Show the correct view
    if ((session()->get('role_id') == 3)) {
        echo view('master_entries/events', $data);
    } else {
        echo view('master_entries/events1', $data);
    }
}

	public function view_events($id)
	{
		
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['events'] = $this->common->select('events', '*', array('event_active' => 1), true);
		$data['event'] = $this->common->select('events', '*', array('event_id' => $id));
		$data['edit'] = 'yes';

		echo view('master_entries/view_events', $data);
	}

// 	public function attempt_add_events($id = null)
// 	{
// 		$mfr_name = trim($this->request->getPost('name'));
	
// 		if (empty($id)) {

//     		$rules = [																					
//     			'name' => ['rules' => 'required|min_length[3]|is_unique[events.event_name]']
//     		];
    	
//     		if (!$this->validate($rules)) {
//     			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    		
//     	    }
//     		$name = $this->common->select('events', '*', array('event_name' => $mfr_name));
//     		if ($name) {
//     			return redirect()->back()->withInput()->with('errors', 'event name already exist');
//     		}
//     	}

// 		$user_id = session()->get('user_id');
// 		$data = [
// 			'event_name' => xss_clean($mfr_name),
// 			'event_user_id' => $user_id,
// 		];
// 		if (!empty($this->request->getFile('event_image'))) {
// 			$folderpath = FCPATH . '/public/uploads/images/events';
// 			$imageFile = $this->request->getFile('event_image');
// 			if ($imageFile->isValid() && ! $imageFile->hasMoved()) {
// 				$filename = $imageFile->getRandomName();
// 				$imageFile->move($folderpath, $filename);
// 				$data['event_image'] = $filename;
// 			}
// 		}

// 		if (!empty($id)) {
// 			$result = $this->common->update_data('events', $data, array('event_id' => $id));
// 		} else {
// 			$result = $this->common->insert_data('events', $data);
// 		}
// 		if ($result) {
// 			if (!empty($id)) {
// 				$msg = 'Event updated successfully!';
// 			} else {
// 				$msg = 'Event added successfully!';
// 			}
// 			return redirect()->to(base_url('admin/events'))->with('success_message', $msg);
// 		} else {
// 			return redirect()->back()->withInput()->with('error', 'Event name already exist');
// 		}
// 	}


    public function attempt_add_events($id = null)
    {
        $mfr_name = trim($this->request->getPost('name'));
        $min_age  = $this->request->getPost('event_min_age');
        $max_age  = $this->request->getPost('event_max_age');
    
        $rules = [
            'event_min_age' => ['label' => 'Min Age', 'rules' => 'permit_empty|numeric|greater_than_equal_to[0]'],
            'event_max_age' => ['label' => 'Max Age', 'rules' => 'permit_empty|numeric|greater_than_equal_to[0]'],
        ];
    
        if (empty($id)) {
            $rules['name'] = ['rules' => 'required|min_length[3]|is_unique[events.event_name]'];
        }
    
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
    
        // Cross-field check: min age must not exceed max age
        if (!empty($min_age) && !empty($max_age) && (int) $min_age > (int) $max_age) {
            return redirect()->back()->withInput()->with('errors', ['event_min_age' => 'Min age cannot be greater than max age']);
        }
    
        if (empty($id)) {
            $name = $this->common->select('events', '*', array('event_name' => $mfr_name));
            if ($name) {
                return redirect()->back()->withInput()->with('errors', 'event name already exist');
            }
        }
    
        $user_id = session()->get('user_id');
        $data = [
            'event_name'    => xss_clean($mfr_name),
            'event_user_id' => $user_id,
            'event_min_age' => !empty($min_age) ? (int) xss_clean($min_age) : null,
            'event_max_age' => !empty($max_age) ? (int) xss_clean($max_age) : null,
        ];
    
        if (!empty($this->request->getFile('event_image'))) {
            $folderpath = FCPATH . '/public/uploads/images/events';
            $imageFile = $this->request->getFile('event_image');
            if ($imageFile->isValid() && !$imageFile->hasMoved()) {
                $filename = $imageFile->getRandomName();
                $imageFile->move($folderpath, $filename);
                $data['event_image'] = $filename;
            }
        }
    
        if (!empty($id)) {
            $result = $this->common->update_data('events', $data, array('event_id' => $id));
        } else {
            $result = $this->common->insert_data('events', $data);
        }
    
        if ($result) {
            if (!empty($id)) {
                $msg = 'Event updated successfully!';
            } else {
                $msg = 'Event added successfully!';
            }
            return redirect()->to(base_url('admin/events'))->with('success_message', $msg);
        } else {
            return redirect()->back()->withInput()->with('error', 'Event name already exist');
        }
    }

	public function payments()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));

		$data['body_class'] = 'skin-default card-no-border';
		$data['commonModel'] = $this->common;

		$org_id = !empty($_GET['organizer']) ? $_GET['organizer'] : '';

		$item_count = $this->tournament->count_payments($org_id);

		$data['total'] = $item_count;
		$per_page = 25;
		$page = $this->request->getGET('page');
		$offset = ($page * $per_page) - $per_page;
		if (empty($page)) {
			$page = 1;
			$offset = 0;
		}
		$data['page'] = $page;
		$data['perPage'] = $per_page;
		$data['payments'] = $this->tournament->get_payments($per_page, $offset, $org_id);

		echo view('admin/payments', $data);
	}

public function booking_history()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', ['user_id' => $user_id]);
		$data['commonModel'] = $this->common;

		$org_id = $this->request->getGet('organizer') ?: NULL;

		$perPage = 25;
		$page    = max(1, (int)($this->request->getGet('page') ?? 1));
		$offset  = ($page - 1) * $perPage;

		$total             = $this->tournament->count_payments($org_id);
		$data['bookings']  = $this->tournament->get_payments($perPage, $offset, $org_id);
		$data['total']     = $total;
		$data['page']      = $page;
		$data['perPage']   = $perPage;

		$db = \Config\Database::connect();
		$data['organizers'] = $db->table('users')
			->where('user_role_id', 3)
			->where('user_active', 1)
			->get()->getResult();

		$data['page_title']    = 'Booking History';
		$data['page_subtitle'] = 'All tournament payment records';
		echo view('admin/booking_history', $data);
	}

	/*------------------------------ General seetings -------------------------*/
	public function general_settings()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$data['page_name'] = 'General Settings';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));

		$free_booking = $this->common->select('settings', 'settings_value', array('settings_variable' => 'free_booking'));
		if (!empty($free_booking)) {
			$data['free_booking'] = $free_booking->settings_value;
		}


		echo view('admin/general_settings', $data);
	}



	public function attempt_general_settings()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$free_booking = !empty($_POST['free_booking']) ? 1 : 0;
		$data = array(
			'settings_value' => $free_booking
		);

		$this->common->update_data('settings', $data, array('settings_variable' => 'free_booking'));
		$msg = 'Setting Updated Successfully';

		return redirect()->to(base_url('admin/general_settings'))->with('success_message', $msg);
	}

	/*------------------------------ Notifications ----------------------------*/

	public function notifications()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['page_name'] = 'Notifications';
		$user_id = session()->get('user_id');
		$data['messages'] = $this->common->select('messages', '*', array('msg_active' => 1), true, 'msg_id DESC');

		echo view('admin/notifications', $data);
	}


	public function get_notifications()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url('admin'));
		}
		$search  = $from = $to = NULL;
		$offset = $_POST['start'];
		$limit = $_POST['length'];
		$draw = $_POST['draw'];
		if (!empty($_POST['search']['value'])) {
			$search = $_POST['search']['value'];
		}

		$bulkData = array();
		$item_count = $this->common->notifications_count($search);
		$customers = $this->common->get_notifications($limit, $offset, $search);

		$bulkData['draw'] = intval($draw);
		$bulkData['recordsTotal'] = $item_count;
		$bulkData['recordsFiltered'] = $item_count;
		$details = array();
		if (!empty($customers)) {
			$i = 1;
			foreach ($customers as $data) {
				$url = '';
				$img = !empty($data->msg_image) ? $data->msg_image : '';
				if (!empty($img)) {
					$url = base_url() . '/public/uploads/images/notifications/' . $img;
				}
				$row = array();
				$row['id'] = $data->msg_id;
								$row['date'] = !empty($data->msg_created_at)?date('d-m-Y', strtotime($data->msg_created_at)):'';
				$row['title'] = !empty($data->msg_title) ? $data->msg_title : '';
				$row['desc'] = !empty($data->msg_desc) ? $data->msg_desc : '';
				$row['image'] = !empty($data->msg_desc) ? '<img src="' . $url . '" height="60"/>' : '';
				$del = !empty($data->msg_id) ? $data->msg_id : '';
				$row['action'] = '<a href="javascript:void(0)" onclick="delete_notification(' . $del . ')" class="btn btn-sm btn-danger">Delete</a>';

				$details[] = $row;
				$i++;
			}
		}
		$bulkData['data'] = $details;
		$bulkData['csrf_token'] = csrf_hash();

		echo json_encode($bulkData);
	}

	public function add_notification()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
				$data['body_class'] = 'skin-default card-no-border';
		$data['page_name'] = 'Create Match';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));

		$data['tournaments'] = $this->common->get_org_tournaments();
	
		echo view('admin/add_notification', $data);
	}


public function attempt_notification()
{
    $rules = [
        'title' => ['label' => 'Title', 'rules' => 'required|min_length[4]'],
        'desc'  => ['label' => 'Description', 'rules' => 'required|min_length[4]']
    ];

    if (! $this->validate($rules)) {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }

    $user_id    = session()->get('user_id');
    $role       = $this->request->getPost('role');
    $title      = $this->request->getPost('title');
    $tournament = $this->request->getPost('tournament');
    $event      = $this->request->getPost('event');
    $group      = $this->request->getPost('group_name');
    $round      = $this->request->getPost('round_name');

    $desc       = xss_clean($this->request->getPost('desc'));
    $date       = date('Y-m-d H:i:s');

    $data = [
        'msg_title'      => $title,
        'msg_desc'       => $desc,
        'msg_created_at' => $date
    ];

    $condition4 = [
        'match_tournament' => $tournament,
        'match_event'      => $event,
        'match_round_id'   => $round,
        'match_group_id'   => $group,
    ];

    // DEBUG: log the exact filter being used to pull matches
    log_message('debug', 'Notification match filter: ' . json_encode($condition4));

    $teams = $this->common->select('matches', '*', $condition4, true);

    // DEBUG: log how many matches were found
    log_message('debug', 'Matches found: ' . (is_array($teams) ? count($teams) : 0));

    $participants = [];

    if ($teams) {
        foreach ($teams as $match) {
            $team1_condition = ['team_id' => $match->match_team1_id];
            $team2_condition = ['team_id' => $match->match_team2_id];

            $team1_participants = $this->common->select('booking', 'book_user_id', $team1_condition, true);
            $team2_participants = $this->common->select('booking', 'book_user_id', $team2_condition, true);

            if (!empty($team1_participants)) {
                foreach ($team1_participants as $p) {
                    $participants[$p->book_user_id] = $p;
                }
            }

            if (!empty($team2_participants)) {
                foreach ($team2_participants as $p) {
                    $participants[$p->book_user_id] = $p;
                }
            }
        }

        $participants = array_values($participants);
    }

    // DEBUG: log how many participants were resolved
    log_message('debug', 'Participants resolved: ' . count($participants));

    if (!empty($participants)) {
        try {
            $instanceId = getenv('PUSHER_INSTANCE_ID') ?: env('PUSHER_INSTANCE_ID');
            $secretKey  = getenv('PUSHER_PRIMARY_KEY') ?: env('PUSHER_PRIMARY_KEY');

            if (empty($instanceId) || empty($secretKey)) {
                log_message('error', 'Pusher Beams credentials missing. instanceId=' . var_export($instanceId, true));
            } else {
                $beamsClient = new PushNotifications([
                    "instanceId" => $instanceId,
                    "secretKey"  => $secretKey,
                ]);

                $interests = [];
                foreach ($participants as $p) {
                    $interests[] = "user-" . $p->book_user_id;
                }

                // DEBUG: log the interests being targeted
                log_message('debug', 'Beams interests: ' . json_encode($interests));

                $payload = [
                    "fcm" => [
                        "notification" => [
                            "title" => $title,
                            "body"  => $desc,
                        ]
                    ],
                    "apns" => [
                        "aps" => [
                            "alert" => [
                                "title" => $title,
                                "body"  => $desc
                            ]
                        ]
                    ],
                    "web" => [
                        "notification" => [
                            "title" => $title,
                            "body"  => $desc,
                        ]
                    ]
                ];

                $publishResponse = $beamsClient->publishToInterests($interests, $payload);

                // DEBUG: log the actual Pusher response
                log_message('debug', 'Beams publish response: ' . json_encode($publishResponse));
            }
        } catch (\Throwable $e) {
            // Catch so a Beams failure never blocks DB save, but we now KNOW about it
            log_message('error', 'Beams publish failed: ' . $e->getMessage());
        }
    } else {
        log_message('debug', 'Skipped push: no participants resolved for this match filter.');
    }

    // Save the notification in DB
    $result = $this->common->insert_data('messages', $data);
    $filename = '';

    if ($result && $_FILES['image']['error'] !== 4) {
        $folderpath = FCPATH . 'public/uploads/images/notifications';
        if (!is_dir($folderpath)) {
            mkdir($folderpath, 0777, true);
        }

        $imageFile = $this->request->getFile('image');
        $filename  = $imageFile->getRandomName();

        if ($imageFile->move($folderpath, $filename)) {
            $this->common->update_data('messages', ['msg_image' => $filename], ['msg_id' => $result]);
        }
    }

    // Create user notifications
    foreach ($participants as $p) {
        $this->common->insert_data('notifications', [
            'notify_msg_id' => $result,
            'notify_for'    => $p->book_user_id,
            'notify_from'   => $user_id,
            'notify_time'   => $date
        ]);
    }

    session()->set('success_message', 'Notification added successfully!');
    return redirect()->to(base_url('admin/notifications'));
}

	public function delete_notification()
	{
		$data = [];
		$notify_id = $this->request->getPost('id');
		if (!empty($notify_id)) {
			try {
				$img = $this->common->select('messages', '*', array('msg_id' => $notify_id));
				if (!empty($img)) {
					if (!empty($img->msg_image)) {
						$image = $img->msg_image;
						$folderpath = FCPATH . 'uploads/images/notifications/' . $image;
						if (file_exists($folderpath)) {
							unlink($folderpath);
						}
					}
				}

				if ($this->common->delete_data('messages', array('msg_id' => $notify_id))) {
					$this->common->delete_data('notifications', array('notify_msg_id' => $notify_id));
				}
				$data['success'] = 'success';
				$data['message'] = 'Notification deleted successfully!';
			} catch (\Exception $e) {
				$data['error'] = 'fail';
				$data['message'] = $e->getMessage();
			}
		} else {
			$data['error'] = 'fail';
			$data['message'] = 'Notification is required';
		}

		echo json_encode($data);
		exit;
	}

	/*-------------------------- Contact us ------------------------------*/
	public function contact_us()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$data['page_name'] = 'Contact Us';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$contact_us = $this->common->select('settings', 'settings_value', array('settings_variable' => 'contact_us'));
		if (!empty($contact_us)) {
			$data['contact_us'] = json_decode($contact_us->settings_value);
		}


		echo view('admin/contact_us', $data);
	}



	public function attempt_contact_us()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}

		$rules = [
			'phone' => ['label' => 'phone', 'rules' => 'required|min_length[5]'],
			'email' => ['label' => 'email', 'rules' => 'required|valid_email'],
			'address' => ['label' => 'address', 'rules' => 'required|min_length[4]']
		];

		if (! $this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}
		$data = array(
			'settings_value' => json_encode($_POST)
		);
		//print_array($data);die();
		$res = $this->common->update_data('settings', $data, array('settings_variable' => 'contact_us'));
		$msg = 'Contact Updated Successfully';

		if (!empty($res)) {
			return redirect()->to(base_url('admin/contact_us'))->with('success_message', $msg);
		} else {
			return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again');
		}
	}

	public function about_us()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['page_name'] = 'About Us';
		$data['about_us'] = $this->common->select('about_us', '*', array('about_us_active' => 1));
		//$data['commonModel'] = $this->common;
		//print_array($data['tournaments']);die();
		echo view('admin/about_us', $data);
	}

	public function attempt_about_us()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}

		$rules = [
			'title'	=> ['label' => 'Title', 'rules' => 'required|min_length[4]'],
			'desc' => ['label' => 'Description', 'rules' => 'required|min_length[4]']
		];

		if (! $this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}
		$title = !empty($_POST['title']) ? $_POST['title'] : NULL;
		$desc = !empty($_POST['desc']) ? xss_clean($_POST['desc']) : NULL;

		$data = [
			'about_us_title' => $title,
			'about_us_desc' => $desc
		];
		$this->common->update_data('about_us', array('about_us_active' => 0), array('about_us_active' => 1));
		$data['about_us_created_at'] = date('Y-m-d H:i:s');
		$result = $this->common->insert_data('about_us', $data);
		$msg = 'About us added successfully!';

		if ($result) {
			return redirect()->to(base_url('admin/about_us'))->with('success_message', $msg);
		} else {
			return redirect()->back()->withInput()->with('error_message', 'Something went wrong. Try again');
		}
	}

	public function faqs()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$data['page_name'] = 'FAQ';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['faqs'] = $this->common->select('faqs', '*', array('faq_active' => 1), true);

		echo view('admin/faqs', $data);
	}

	public function edit_faq($id)
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';

		$data['page_name'] = 'FAQ';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['faqs'] = $this->common->select('faqs', '*', array('faq_active' => 1), true);
		$data['faq'] = $this->common->select('faqs', '*', array('faq_id' => $id));

		echo view('admin/faqs', $data);
	}

	public function attempt_faq()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}

		$rules = [
			'question'	=> ['label' => 'Question', 'rules' => 'required|min_length[4]'],
			'answer' => ['label' => 'Answer', 'rules' => 'required|min_length[4]']
		];

		if (! $this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}
		$faq_id = !empty($_POST['faq_id']) ? $_POST['faq_id'] : NULL;

		$question = !empty($_POST['question']) ? xss_clean($_POST['question']) : NULL;
		$answer = !empty($_POST['answer']) ? xss_clean($_POST['answer']) : NULL;
		$data = [
			'faq_question' => $question,
			'faq_answer' => $answer
		];
		if (!empty($faq_id)) {
			$data['faq_updated_at'] = date('Y-m-d H:i:s');
			$result = $this->common->update_data('faqs', $data, array('faq_id' => $faq_id));
			$msg = 'Faq updated successfully!';
		} else {
			$data['faq_created_at'] = date('Y-m-d H:i:s');
			$result = $this->common->insert_data('faqs', $data);
			$msg = 'Faq added successfully!';
		}

		if ($result) {
			return redirect()->to(base_url('admin/faqs'))->with('success_message', $msg);
		} else {
			return redirect()->back()->withInput()->with('error_message', 'Something went wrong. Try again');
		}
	}

	public function delete_faq()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}

		$data = [];
		$faq_id = $this->request->getPost('faq_id');
		if (!empty($faq_id)) {
			try {

				$this->common->delete_data('faqs', array('faq_id' => $faq_id));
				$data['success'] = 'success';
				$data['message'] = 'FAQ deleted successfully!';
			} catch (\Exception $e) {

				$data['error'] = 'fail';
				$data['message'] = 'Something went wrong. Try again.';
			}
		} else {
			$data['error'] = 'fail';
			$data['message'] = 'FAQ Id is required';
		}

		echo json_encode($data);
		exit;
	}

	public function terms_conditions()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';

		$data['page_name'] = 'Terms & Conditions';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['terms_conditions'] = $this->common->select('terms_conditions', '*', array('terms_conditions_active' => 1));

		echo view('admin/terms_conditions', $data);
	}

	public function attempt_terms_conditions()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}

		$rules = [
			'desc' => ['label' => 'Description', 'rules' => 'required|min_length[4]']
		];

		if (! $this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}
		$desc = !empty($_POST['desc']) ? xss_clean($_POST['desc']) : NULL;

		$data = [
			'terms_conditions_desc' => $desc
		];
		$this->common->update_data('terms_conditions', array('terms_conditions_active' => 0), array('terms_conditions_active' => 1));
		$data['terms_conditions_created_at'] = date('Y-m-d H:i:s');
		$result = $this->common->insert_data('terms_conditions', $data);
		$msg = 'Terms and conditions added successfully!';

		if ($result) {
			return redirect()->to(base_url('admin/terms_conditions'))->with('success_message', $msg);
		} else {
			return redirect()->back()->withInput()->with('error_message', 'Something went wrong. Try again');
		}
	}

	public function privacy_policy()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';

		$data['page_name'] = 'Privacy Policy';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['privacy_policy'] = $this->common->select('privacy_policy', '*', array('privacy_policy_active' => 1));

		echo view('admin/privacy_policy', $data);
	}

	public function attempt_privacy_policy()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}

		$rules = [
			'desc' => ['label' => 'Description', 'rules' => 'required|min_length[4]']
		];

		if (! $this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}
		$desc = !empty($_POST['desc']) ? xss_clean($_POST['desc']) : NULL;

		$data = [
			'privacy_policy_desc' => $desc
		];
		$this->common->update_data('privacy_policy', array('privacy_policy_active' => 0), array('privacy_policy_active' => 1));
		$data['privacy_policy_created_at'] = date('Y-m-d H:i:s');
		$result = $this->common->insert_data('privacy_policy', $data);
		$msg = 'Privacy policy added successfully!';

		if ($result) {
			return redirect()->to(base_url('admin/privacy_policy'))->with('success_message', $msg);
		} else {
			return redirect()->back()->withInput()->with('error_message', 'Something went wrong. Try again');
		}
	}


	public function getEvents()
	{
		$results = [];
		$listing_id = $this->request->getPost('listing_id');
		if (!empty($listing_id)) {
			$results = $this->common->select('listing_events', '*', array('listing_event_listing_id' => $listing_id), true);

			if ($results) {
				foreach ($results as $row) {
					$row->event_name = '';
					$event = $this->common->select('events', '*', array('event_id' => $row->listing_event_title));
					if (!empty($event->event_name)) {
						$row->event_name = $event->event_name;
					}
				}
				echo json_encode(array('success' => 1, 'msg' => $results, 'csrf_token' => csrf_hash()));
			} else {
				echo json_encode(array('success' => 0, 'msg' => $results, 'csrf_token' => csrf_hash()));
			}
		} else {
			echo json_encode(array('success' => 0, 'msg' => $results, 'csrf_token' => csrf_hash()));
		}
	}
	public function refer_back(){
	if (session()->get('role_id') != 1) {
		return redirect()->to(base_url());
	}
	$subject = $this->request->getPost('subject');
	$message = $this->request->getPost('message');
	$user_id = session()->get('user_id');
	$data['users'] = $this->common->refer_back($user_id , $subject , $message);
	echo view('admin/privacy_policy', $data);
}
public function add_districts()
{
    if (session()->get('role_id') != 1) {
        return redirect()->to(base_url());
    }

    $page = $this->request->getVar('page') ?? 1;
    $perPage = 10;

    $data['body_class'] = 'skin-default card-no-border';
    $user_id = session()->get('user_id');
    $data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
    $data['countries'] = $this->common->select('countries', '*', array('country_active' => 1), true);
    $data['states'] = $this->common->select('state', '*', array('state_active' => 1), true);

    $total = $this->common->count_data('districts', array('district_active' => 1));

    $pager = \Config\Services::pager();
    $offset = ($page - 1) * $perPage;
		$data['offset'] = $offset;

    $data['districts'] = $this->common->get_limit_data('districts', '*', array('district_active' => 1), $perPage, $offset);
    $data['pager'] = $pager->makeLinks($page, $perPage, $total, 'default_full'); // Use any template: 'default_full', 'simple', or custom
    $data['commonModel'] = $this->common;

    echo view('master_entries/districts', $data);
}


	public function edit_district($id)
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['countries'] = $this->common->select('countries', '*', array('country_active' => 1), true);
		$data['states'] = $this->common->select('state', '*', array('state_active' => 1), true);
		$data['commonModel'] = $this->common;
		   $total = $this->common->count_data('districts', array('district_active' => 1));
  $page = $this->request->getVar('page') ?? 1;
    $perPage = 10;

    $pager = \Config\Services::pager();
    $offset = ($page - 1) * $perPage;
    		$data['offset'] = $offset;

    $data['pager'] = $pager->makeLinks($page, $perPage, $total, 'default_full'); 
		$data['districts'] = $this->common->get_limit_data('districts', '*', array('district_active' => 1), $perPage, $offset);
		$data['city'] = $this->common->select('districts', '*', array('district_id' => $id));
		$data['edit'] = 'yes';
		echo view('master_entries/districts', $data);
	}
	public function delete_district($id)
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data = DB::table('districts')
		->where('district_id', $id)
		->delete();	
		$data['body_class'] = 'skin-default card-no-border';
	$user_id = session()->get('user_id');
	$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
	$data['countries'] = $this->common->select('countries', '*', array('country_active' => 1), true);
	$data['states'] = $this->common->select('state', '*', array('state_active' => 1), true);
	$data['districts'] = $this->common->select('districts', '*', array('district_active' => 1), true);
	$data['commonModel'] = $this->common;
	
		echo view('master_entries/districts', $data);
	}
	public function notification()
	{
				$data = [];
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['page_name'] = 'Notifications';
		$data['messages'] = $this->common->select('messages', '*', array('msg_active' => 1), true, 'msg_id DESC');
    	$data['header'] = TRUE;
        $data['footer'] = TRUE;
        $contact_us= $this->common->select('settings','settings_value',array('settings_variable'=>'contact_us'));
        if(!empty($contact_us)){
            $data['contact_us'] = json_decode($contact_us->settings_value);
        }
    	$data['commonModel'] = $this->common;
        $data['famous_region'] = $this->common->select('cities','*',array('city_active'=>1),true);
        $user_id = session()->get('user_id');
        $data['users'] = $this->common->select('users','*',array('user_id'=>$user_id));

        $data['home_banners'] = $this->common->select('home_banner','*',array('home_banner_active'=>1),true);
        $data['sports'] = $this->common->select('sports','*',array('sports_active'=>1),true);
        $city_id = !empty(session()->get('city_id'))?session()->get('city_id'):NULL;
        $country = !empty(session()->get('country_id'))?session()->get('country_id'):NULL;

        $sports_id = !empty($this->request->getGET('sports'))?$this->request->getGET('sports'):NULL;
        $completed = !empty($this->request->getGET('status'))?$this->request->getGET('status'):NULL;
        //echo $completed;die();
        $query = !empty($this->request->getGET('q'))?$this->request->getGET('q'):NULL;
        
        //echo $completed_date;die();
        $item_count = $this->tournament->count_all_listings($city_id, $country, $sports_id, $completed, $query);
        //echo $item_count;exit;
        $data['total'] = $item_count;
        $per_page = 25;
        $page = $this->request->getGET('page');
        $offset = ($page*$per_page)-$per_page;
        if(empty($page)){
            $page = 1;
            $offset = 0;

        }
        $data['page'] = $page;
        $data['perPage'] = $per_page;
        $data['tournaments'] = $this->tournament->get_tournament_home($per_page, $offset, $city_id, $country, $sports_id, $completed, $query);
		echo view('home',$data);
	}
	
	public function posts()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$user_id = session()->get('user_id');
				$data['body_class'] = 'skin-default card-no-border';
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['commonModel'] = $this->common;
		$db = \Config\Database::connect();
		$data['posts'] = $db->table('posts')
			->select('posts.*,users.user_name')
			->join('users', 'users.user_id = posts.post_user_id')
			->orderBy('posts.post_id','desc')
			->get()
   		 ->getResult();
   		 
		echo view('master_entries/posts', $data);
	}
	
    public function users()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$user_id = session()->get('user_id');
		$data['body_class'] = 'skin-default card-no-border';
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['commonModel'] = $this->common;
		$db = \Config\Database::connect();
		$db = \Config\Database::connect();
		$builder = $db->table('users u');
		$builder->where('u.user_role_id', 2);
        $data['user'] = $builder->get()->getResult();
		echo view('master_entries/users', $data);
	}
	
	
	public function umpire()
    {
        if (session()->get('role_id') != 1) {
            return redirect()->to(base_url());
        }
    
        $data['page_name']     = 'Manage Umpires';
        $data['page_title']    = 'Umpires';
        $data['page_subtitle'] = 'Manage umpires across all tournaments';
    
        $search = $this->request->getGET('search') ?? '';
    
        $data['commonModel'] = $this->common;
    
        // Admin sees ALL umpires, not scoped to a single organizer's user_id
        $item_count = $this->common->count_umpire_list(null);
        $data['total'] = $item_count;
    
        $per_page = 25;
        $page = $this->request->getGET('page');
        $offset = ($page * $per_page) - $per_page;
        if (empty($page)) {
            $page = 1;
            $offset = 0;
        }
    
        $data['page']        = $page;
        $data['perPage']     = $per_page;
        $data['umpire_list'] = $this->common->get_umpire_list_data($per_page, $offset, null, $search);
    
        return view('master_entries/umpire_list', $data);
    }
	
	
	
	public function clearAll()
    {
        
       helper('filesystem');
    
        delete_files(WRITEPATH . 'cache/', false);
    
        return "Application cache cleared successfully.";
    }

}