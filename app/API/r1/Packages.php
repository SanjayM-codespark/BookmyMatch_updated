<?php
namespace App\API\r1;
use CodeIgniter\RESTful\ResourceController;
use App\Traits\ReqresTrait;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class Packages extends ResourceController
{
    use ReqresTrait;
 
    protected $db;
    protected $razorpay;
 
    public function __construct()
    {
        helper(['form', 'url', 'custom', 'number']);
        $this->db = \Config\Database::connect();
 
        $this->razorpay = new Api(getenv('RAZORPAY_KEY_ID'), getenv('RAZORPAY_KEY_SECRET'));
    }
 
    public function get_packages()
    {
        $packages = $this->db->table('packages')
            ->where('package_active', 1)
            ->orderBy('package_sort_order', 'ASC')
            ->get()->getResult();
 
        if (!empty($packages)) {
            return $this->getResponse([
                'status'  => true,
                'message' => 'Success',
                'data'    => $packages,
            ]);
        }
 
        return $this->getResponse([
            'status'  => false,
            'message' => 'No packages found',
            'data'    => [],
        ]);
    }
 
    public function my_membership()
    {
        $user_id = $this->request->getGet('user_id');
        if (empty($user_id)) {
            return $this->getResponse(['status' => false, 'message' => 'user_id is required']);
        }
 
        $row = $this->db->query("
            SELECT m.*, p.package_name, p.package_description, p.package_duration_type,
                   p.package_max_matches, p.package_max_tournaments, p.package_max_teams,
                   p.package_can_create_tournament, p.package_priority_booking
            FROM memberships m
            JOIN packages p ON p.package_id = m.membership_package_id
            WHERE m.membership_user_id = ? AND m.membership_status = 'active'
            ORDER BY m.membership_id DESC
            LIMIT 1
        ", [$user_id])->getRow();
 
        if (!empty($row)) {
            return $this->getResponse([
                'status'  => true,
                'message' => 'Success',
                'data'    => $row,
            ]);
        }
 
        return $this->getResponse([
            'status'  => false,
            'message' => 'No active membership',
            'data'    => null,
        ]);
    }
 
    
    public function create_package_order()
    {
        $input      = $this->getRequestInput($this->request);
        $user_id    = $input['user_id']    ?? null;
        $package_id = $input['package_id'] ?? null;
 
        if (empty($user_id) || empty($package_id)) {
            return $this->getResponse(['status' => false, 'message' => 'user_id and package_id are required']);
        }
 
        $package = $this->db->table('packages')
            ->where('package_id', $package_id)
            ->where('package_active', 1)
            ->get()->getRow();
 
        if (empty($package)) {
            return $this->getResponse(['status' => false, 'message' => 'Package not found or inactive']);
        }
 
        $amount_in_paise = (int) round(((float) $package->package_price) * 100);
 
        // ---- FREE PACKAGE: skip Razorpay entirely, activate right away ----
        if ($amount_in_paise <= 0) {
            $result = $this->activateMembership($user_id, $package_id, 'FREE', 0);
 
            return $this->getResponse([
                'status'         => true,
                'message'        => 'Free membership activated',
                'is_free'        => true,
                'membership_id'  => $result['membership_id'],
                'start_date'     => $result['start_date'],
                'end_date'       => $result['end_date'],
                'package_name'   => $package->package_name,
            ]);
        }
 
        // ---- PAID PACKAGE: create Razorpay order as usual ----
        try {
            $razorpay_order = $this->razorpay->order->create([
                'receipt'         => 'pkg_' . $package_id . '_' . time(),
                'amount'          => $amount_in_paise,
                'currency'        => 'INR',
                'payment_capture' => 1,
            ]);
        } catch (\Exception $e) {
            return $this->getResponse(['status' => false, 'message' => 'Unable to create payment order: ' . $e->getMessage()]);
        }
 
        $this->db->table('payment_orders')->insert([
            'user_id'           => $user_id,
            'package_id'        => $package_id,
            'razorpay_order_id' => $razorpay_order['id'],
            'amount'            => $amount_in_paise,
            'currency'          => 'INR',
            'status'            => 'created',
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ]);
 
        return $this->getResponse([
            'status'    => true,
            'message'   => 'Order created',
            'is_free'   => false,
            'data'      => [
                'razorpay_order_id' => $razorpay_order['id'],
                'razorpay_key'      => getenv('RAZORPAY_KEY_ID'),
                'amount'            => $amount_in_paise,
                'currency'          => 'INR',
                'package_name'      => $package->package_name,
            ],
        ]);
    }
 
    public function verify_package_payment()
    {
        $input               = $this->getRequestInput($this->request);
        $razorpay_order_id   = $input['razorpay_order_id']   ?? null;
        $razorpay_payment_id = $input['razorpay_payment_id'] ?? null;
        $razorpay_signature  = $input['razorpay_signature']  ?? null;
 
        if (empty($razorpay_order_id) || empty($razorpay_payment_id) || empty($razorpay_signature)) {
            return $this->getResponse(['status' => false, 'message' => 'Missing payment verification fields']);
        }
 
        $order_row = $this->db->table('payment_orders')
            ->where('razorpay_order_id', $razorpay_order_id)
            ->get()->getRow();
 
        if (empty($order_row)) {
            return $this->getResponse(['status' => false, 'message' => 'Order not found']);
        }
 
        if ($order_row->status === 'paid') {
            return $this->getResponse(['status' => false, 'message' => 'This order has already been processed']);
        }
 
        try {
            $this->razorpay->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $razorpay_order_id,
                'razorpay_payment_id' => $razorpay_payment_id,
                'razorpay_signature'  => $razorpay_signature,
            ]);
        } catch (SignatureVerificationError $e) {
            $this->db->table('payment_orders')
                ->where('id', $order_row->id)
                ->update(['status' => 'failed', 'updated_at' => date('Y-m-d H:i:s')]);
 
            return $this->getResponse(['status' => false, 'message' => 'Payment verification failed']);
        }
 
        $this->db->table('payment_orders')
            ->where('id', $order_row->id)
            ->update([
                'razorpay_payment_id' => $razorpay_payment_id,
                'status'              => 'paid',
                'updated_at'          => date('Y-m-d H:i:s'),
            ]);
 
        $price_paid = $order_row->amount / 100;
 
        $result = $this->activateMembership($order_row->user_id, $order_row->package_id, $razorpay_payment_id, $price_paid);
 
        return $this->getResponse([
            'status'        => true,
            'message'       => 'Payment verified and membership activated',
            'membership_id' => $result['membership_id'],
            'start_date'    => $result['start_date'],
            'end_date'      => $result['end_date'],
        ]);
    }
 
    // POST api/buy_package
    // Body: user_id, package_id
    // IMPORTANT: this endpoint only works for FREE packages (package_price == 0).
    // Paid packages MUST go through create_package_order -> Razorpay checkout -> verify_package_payment.
    // This prevents a client from bypassing payment by calling buy_package directly on a paid package.
    public function buy_package()
    {
        $input      = $this->getRequestInput($this->request);
        $user_id    = $input['user_id']    ?? null;
        $package_id = $input['package_id'] ?? null;
 
        if (empty($user_id) || empty($package_id)) {
            return $this->getResponse(['status' => false, 'message' => 'user_id and package_id are required']);
        }
 
        $package = $this->db->table('packages')
            ->where('package_id', $package_id)
            ->where('package_active', 1)
            ->get()->getRow();
 
        if (empty($package)) {
            return $this->getResponse(['status' => false, 'message' => 'Package not found or inactive']);
        }
 
        // Block paid packages from this endpoint
        if ((float) $package->package_price > 0) {
            return $this->getResponse([
                'status'  => false,
                'message' => 'This package requires payment. Use create_package_order instead.',
            ]);
        }
 
        $result = $this->activateMembership($user_id, $package_id, 'FREE', 0);
 
        return $this->getResponse([
            'status'        => true,
            'message'       => 'Free membership activated successfully',
            'membership_id' => $result['membership_id'],
            'start_date'    => $result['start_date'],
            'end_date'      => $result['end_date'],
        ]);
    }
 
    private function activateMembership($user_id, $package_id, $payment_ref, $price_paid)
    {
        $package = $this->db->table('packages')
            ->where('package_id', $package_id)
            ->get()->getRow();
 
        $this->db->table('memberships')
            ->where('membership_user_id', $user_id)
            ->where('membership_status', 'active')
            ->update(['membership_status' => 'cancelled']);
 
        $start_date = date('Y-m-d');
        if ($package->package_duration_days == 0) {
            $end_date = null; // lifetime package
        } else {
            $end_date = date('Y-m-d', strtotime('+' . $package->package_duration_days . ' days'));
        }
 
        $this->db->table('memberships')->insert([
            'membership_user_id'     => $user_id,
            'membership_package_id'  => $package_id,
            'membership_start_date'  => $start_date,
            'membership_end_date'    => $end_date,
            'membership_status'      => 'active',
            'membership_payment_ref' => $payment_ref,
            'membership_price_paid'  => $price_paid,
            'membership_created_at'  => date('Y-m-d H:i:s'),
            'membership_updated_at'  => date('Y-m-d H:i:s'),
        ]);
 
        $membership_id = $this->db->insertID();
 
        return [
            'membership_id' => $membership_id,
            'start_date'    => $start_date,
            'end_date'      => $end_date ?? 'Lifetime',
        ];
    }

    // POST api/cancel_membership
    // Body: user_id, membership_id
    public function cancel_membership()
    {
        $user_id       = $this->getRequestInput($this->request)['user_id']       ?? null;
        $membership_id = $this->getRequestInput($this->request)['membership_id'] ?? null;

        if (empty($user_id) || empty($membership_id)) {
            return $this->getResponse(['status' => false, 'message' => 'user_id and membership_id are required']);
        }

        $updated = $this->db->table('memberships')
            ->where('membership_id', $membership_id)
            ->where('membership_user_id', $user_id)
            ->where('membership_status', 'active')
            ->update(['membership_status' => 'cancelled', 'membership_updated_at' => date('Y-m-d H:i:s')]);

        if ($updated) {
            return $this->getResponse(['status' => true, 'message' => 'Membership cancelled']);
        }

        return $this->getResponse(['status' => false, 'message' => 'Membership not found or already inactive']);
    }

    // GET api/membership_history?user_id=:num
    // Full membership history for the user
    public function membership_history()
    {
        $user_id = $this->request->getGet('user_id');
        if (empty($user_id)) {
            return $this->getResponse(['status' => false, 'message' => 'user_id is required']);
        }

        $rows = $this->db->query("
            SELECT m.*, p.package_name, p.package_duration_type, p.package_price
            FROM memberships m
            JOIN packages p ON p.package_id = m.membership_package_id
            WHERE m.membership_user_id = ?
            ORDER BY m.membership_id DESC
        ", [$user_id])->getResult();

        return $this->getResponse([
            'status'  => true,
            'message' => 'Success',
            'data'    => $rows ?? [],
        ]);
    }
}