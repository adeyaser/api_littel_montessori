<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use \Firebase\JWT\JWT;

class Walimurid extends RestController {

    // KUNCI RAHASIA UNTUK JWT - Pastikan Anda menggantinya bila masuk production
    private $jwt_secret_key = 'rahasia_little_home_super_aman_123';

    public function __construct()
    {
        // Headers for CORS
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization, Access-Control-Request-Method');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
        
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }

        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'string']);
    }

    /**
     * VERIFIKASI JWT TOKEN
     * Method internal untuk mengecek token Bearer dari Header HTTP
     */
    private function _verify_jwt()
    {
        $authHeader = $this->input->get_request_header('Authorization');
        
        if ($authHeader) {
            // Ekstrak token dari string "Bearer <token JWT>"
            if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $token = $matches[1];
                try {
                    $decoded = JWT::decode($token, $this->jwt_secret_key, ['HS256']);
                    return $decoded;
                } catch (\Exception $e) {
                    $this->response([
                        'status' => FALSE, 
                        'message' => 'Token invalid atau kedaluwarsa (' . $e->getMessage() . ')'
                    ], 401);
                    exit();
                }
            }
        }
        
        $this->response([
            'status' => FALSE, 
            'message' => 'Akses ditolak, token otorisasi Bearer tidak ditemukan'
        ], 401);
        exit();
    }

    /**
     * 1. LOGIN WALI MURID
     * Method: POST /walimurid/login
     */
    public function login_post()
    {
        $email    = $this->post('email');
        $password = $this->post('password');

        if (empty($email) || empty($password)) {
            return $this->response(['status' => FALSE, 'message' => 'Email dan Password wajib diisi'], 400);
        }

        $user = $this->db->get_where('users', [
            'email' => $email, 
            'role_id' => 5, 
            'status_aktif' => '1'
        ])->row_array();

        if ($user) {
            if (password_verify($password, $user['password']) || $password == $user['password_hint']) {
                
                $pengasuh = $this->db->get_where('keluarga_pengasuh', ['id' => $user['id_pengguna']])->row_array();
                $murid_id = $pengasuh ? $pengasuh['murid_id'] : null;

                // BUAT PAYLOAD JWT
                $payload = [
                    'iss'      => base_url(), // Issuer (yang menerbitkan)
                    'iat'      => time(), // Waktu token dibuat
                    'exp'      => time() + (60 * 60 * 24 * 7), // Expired dalam 7 hari
                    'uid'      => $user['id'],
                    'email'    => $user['email'],
                    'role_id'  => $user['role_id'],
                    'murid_id' => $murid_id
                ];
                
                // ENCODE KE DALAM BENTUK BENTUK TOKEN JWT
                $token = JWT::encode($payload, $this->jwt_secret_key, 'HS256');

                // Hilangkan sensitive data dari response
                unset($user['password']);
                unset($user['password_hint']);

                $murid = null;
                if ($murid_id) {
                    $murid = $this->db->get_where('murid', ['id' => $murid_id])->row_array();
                }

                $this->response([
                    'status'   => TRUE,
                    'message'  => 'Login Berhasil',
                    'token'    => $token, // JWT TOKEN KIRIM KE MOBILE APP
                    'data'     => [
                        'user'  => $user,
                        'murid' => $murid
                    ]
                ], 200);

            } else {
                $this->response(['status' => FALSE, 'message' => 'Password yang Anda masukkan salah'], 401);
            }
        } else {
            $this->response(['status' => FALSE, 'message' => 'Email tidak terdaftar atau akun tidak aktif'], 404);
        }
    }

    /**
     * 2. DASHBOARD DATA
     * Method: GET /walimurid/dashboard
     */
    public function dashboard_get()
    {
        $this->_verify_jwt(); // Wajib JWT

        $murid_id = $this->get('id_anak');
        if (!$murid_id) return $this->response(['status' => FALSE, 'message' => 'id_anak diperlukan'], 400);

        $murid = $this->db->get_where('murid', ['id' => $murid_id])->row_array();
        if (!$murid) return $this->response(['status' => FALSE, 'message' => 'Anak tidak ditemukan'], 404);

        $this->db->where('id_anak', $murid_id);
        $this->db->order_by('tanggal', 'DESC');
        $this->db->limit(3);
        $recent_pencatatan = $this->db->get('pencatatan')->result_array();

        $this->db->like('id_anak', $murid_id);
        $this->db->order_by('tanggal', 'DESC');
        $this->db->limit(3);
        $recent_post = $this->db->get('tpost')->result_array();

        // Tambahkan content/image beserta URL lengkap ke dalam dashboard terbaru
        foreach ($recent_post as &$post) {
            $post_content = $this->db->get_where('tpost_content', ['tpost_id' => $post['id']])->result_array();
            foreach ($post_content as &$content) {
                if (!empty($content['file_url'])) {
                    $content['file_url'] = 'https://galerilittlehomemontessori.my.id/uploads/' . $content['file_url'];
                }
            }
            $post['content'] = $post_content;

            if (!empty($post['deskripsi'])) {
                $post['deskripsi'] = mb_convert_encoding($post['deskripsi'], 'UTF-8', 'UTF-8');
            }
        }

        $this->db->where('id_anak', $murid_id);
        $this->db->where('status !=', 'Lunas');
        $this->db->select_sum('nominal');
        $tagihan_pending = $this->db->get('tth_pemasukan')->row()->nominal;

        $this->response([
            'status' => TRUE,
            'data'   => [
                'murid'           => $murid,
                'tagihan_pending' => $tagihan_pending ?? 0,
                'latest_records'  => [
                    'pencatatan' => $recent_pencatatan,
                    'posting'    => $recent_post
                ]
            ]
        ], 200);
    }

    /**
     * 3. DATA PENCATATAN
     * Method: GET /walimurid/pencatatan
     */
    public function pencatatan_get()
    {
        $this->_verify_jwt(); // Wajib JWT

        $murid_id = $this->get('id_anak');
        if (!$murid_id) return $this->response(['status' => FALSE, 'message'=> 'id_anak diperlukan'], 400);

        $this->db->where('id_anak', $murid_id);
        $this->db->order_by('tanggal', 'DESC');
        $data = $this->db->get('pencatatan')->result_array();

        $this->response(['status' => TRUE, 'data' => $data], 200);
    }

    /**
     * 4. DATA POSTING
     * Method: GET /walimurid/posting
     */
    public function posting_get()
    {
        $this->_verify_jwt(); // Wajib JWT

        $murid_id = $this->get('id_anak');
        if (!$murid_id) return $this->response(['status' => FALSE, 'message' => 'id_anak diperlukan'], 400);

        $this->db->like('id_anak', $murid_id);
        $this->db->order_by('tanggal', 'DESC');
        $posts = $this->db->get('tpost')->result_array();

        foreach ($posts as &$post) {
            $post_content = $this->db->get_where('tpost_content', ['tpost_id' => $post['id']])->result_array();
            
            // Tambahkan URL lengkap untuk file gambar
            foreach ($post_content as &$content) {
                if (!empty($content['file_url'])) {
                    $content['file_url'] = 'https://galerilittlehomemontessori.my.id/uploads/' . $content['file_url'];
                }
            }
            $post['content'] = $post_content;
            
            // Konversi ke UTF-8 ini saya biarkan aktif agar API tidak 'blank' lagi akibat Malformed JSON
            // Jika tidak menggunakan mb_convert_encoding, json_encode() akan me-return response kosong jika
            // di database ada karakter aneh/latin. Sanitasi HTML, newline dihapus seluruhnya sesuai request.
            if (!empty($post['deskripsi'])) {
               $post['deskripsi'] = mb_convert_encoding($post['deskripsi'], 'UTF-8', 'UTF-8');
            }
        }

        $this->response(['status' => TRUE, 'data' => $posts], 200);
    }

    /**
     * 5. TAGIHAN ANAK (KEUANGAN / PEMASUKAN)
     * Method: GET /walimurid/tagihan
     */
    public function tagihan_get()
    {
        $this->_verify_jwt(); // Wajib JWT

        $murid_id = $this->get('id_anak');
        if (!$murid_id) return $this->response(['status' => FALSE, 'message' => 'id_anak diperlukan'], 400);

        $this->db->where('id_anak', $murid_id);
        $this->db->order_by('tanggal', 'DESC');
        $tagihan = $this->db->get('tth_pemasukan')->result_array();

        $this->response(['status' => TRUE, 'data' => $tagihan], 200);
    }

    /**
     * 6. TESTIMONI GET
     * Method: GET /walimurid/testimoni
     */
    public function testimoni_get()
    {
        $this->_verify_jwt(); // Wajib JWT

        $this->db->order_by('id', 'DESC');
        $testimoni = $this->db->get('testimonial')->result_array();
        $this->response(['status' => TRUE, 'data' => $testimoni], 200);
    }

    /**
     * 7. POST TESTIMONI
     * Method: POST /walimurid/testimoni
     */
    public function testimoni_post()
    {
        $jwt = $this->_verify_jwt(); // Wajib JWT

        // Deteksi key dari body request untuk menyesuaikan ke database ('name', 'profesi', 'description')
        $name        = $this->post('name') ?? $this->post('nama');
        $profesi     = $this->post('profesi') ?? ($this->post('jabatan') ?: 'Wali Murid');
        $description = $this->post('description') ?? $this->post('testimoni');

        if (empty($name) || empty($description)) {
             return $this->response(['status' => FALSE, 'message' => 'Parameter name dan description wajib diisi'], 400);
        }

        $data = [
            'name'        => $name,
            'profesi'     => $profesi,
            'description' => $description,
            'id_user'     => $jwt->uid,
            'img'         => 'default.png'
        ];
        
        if ($this->db->insert('testimonial', $data)) {
            $this->response(['status' => TRUE, 'message' => 'Testimoni berhasil disimpan'], 200);
        }
    }

    /**
     * 8. GLOBAL SEARCH
     * Method: GET /walimurid/search?q=&id_anak=
     */
    public function search_get()
    {
        $this->_verify_jwt(); // Wajib JWT

        $q = $this->get('q');
        $murid_id = $this->get('id_anak');
        
        if (empty($q) || empty($murid_id)) {
            return $this->response(['status' => FALSE, 'message' => 'Parameter q dan id_anak diperlukan'], 400);
        }

        $this->db->where('id_anak', $murid_id);
        $this->db->like('kegiatan', $q);
        $pencatatan = $this->db->get('pencatatan')->result_array();

        $this->db->like('id_anak', $murid_id);
        $this->db->like('judul', $q);
        $posting = $this->db->get('tpost')->result_array();

        $this->response([
            'status' => TRUE,
            'data'   => [
                'pencatatan' => $pencatatan,
                'posting'    => $posting
            ]
        ], 200);
    }

    /**
     * 9. KOMENTAR GET
     * Method: GET /walimurid/komentar
     */
    public function komentar_get()
    {
        $this->_verify_jwt(); // Wajib JWT

        $post_id = $this->get('post_id');
        if (!$post_id) return $this->response(['status' => FALSE, 'message' => 'post_id diperlukan'], 400);

        $this->db->where('post_id', $post_id);
        $this->db->order_by('created_at', 'ASC');
        $komentar = $this->db->get('comments')->result_array();

        $this->response(['status' => TRUE, 'data' => $komentar], 200);
    }

    /**
     * 10. POST KOMENTAR
     * Method: POST /walimurid/komentar
     */
    public function komentar_post()
    {
        $jwtData = $this->_verify_jwt(); // Verifikasi JWT

        $post_id = $this->post('post_id');
        $author  = $this->post('author') ?: $jwtData->email; 
        $content = $this->post('content');
        
        if (empty($post_id) || empty($content)) {
             return $this->response(['status' => FALSE, 'message' => 'post_id, dan content wajib diisi'], 400);
        }

        $data = [
            'post_id'    => $post_id,
            'author'     => $author,
            'content'    => $content,
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->db->insert('comments', $data)) {
            $this->response([
                'status'  => TRUE,
                'message' => 'Komentar berhasil ditambahkan',
                'data'    => $data
            ], 200);
        } else {
            $this->response(['status' => FALSE, 'message' => 'Gagal menambahkan komentar'], 500);
        }
    }

    /**
     * 10. PROFILE WALI MURID
     * Method: GET /walimurid/profile
     */
    public function profile_get()
    {
        $jwt = $this->_verify_jwt(); // Wajib JWT

        // 1. Ambil data akun wali murid dari tabel users (Tanpa password)
        $this->db->select('id, nama, telepon, email, alamat, perusahaan, negara, job, img, tentang, status_aktif, id_pengguna');
        $user = $this->db->get_where('users', ['id' => $jwt->uid])->row_array();

        if (!$user) {
            return $this->response(['status' => FALSE, 'message' => 'Profil Wali Murid tidak ditemukan'], 404);
        }

        // Jika foto menggunakan URL relatif (bukan http), kita konversi jadi absolute URL
        if (!empty($user['img']) && !preg_match("/^http/", $user['img'])) {
            $user['img'] = 'https://galerilittlehomemontessori.my.id/uploads/galeri/' . $user['img']; // Sesuaikan folder di server Anda
        }

        // 2. Ambil data keluarga pengasuh dari tabel keluarga_pengasuh
        $keluarga = null;
        $murid = null;

        if (!empty($user['id_pengguna'])) {
            $keluarga = $this->db->get_where('keluarga_pengasuh', ['id' => $user['id_pengguna']])->row_array();

            // 3. Ambil data anak (murid)
            if (!empty($keluarga['murid_id'])) {
                $murid = $this->db->get_where('murid', ['id' => $keluarga['murid_id']])->row_array();
            }
        }

        $this->response([
            'status' => TRUE,
            'message'=> 'Berhasil memuat profil',
            'data'   => [
                'user'     => $user,
                'keluarga' => $keluarga,
                'anak'     => $murid
            ]
        ], 200);
    }
}
