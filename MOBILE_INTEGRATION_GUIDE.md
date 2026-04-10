# 📱 MOBILE INTEGRATION GUIDE
**Little Home API - React Native, Flutter, Android Studio**

---

## 🎯 API ENDPOINTS - OPTIMIZED FOR MOBILE

Semua endpoint sudah dioptimasi untuk performa mobile dengan pagination, compression, dan caching.

### Base URL
```
https://littlehome-api.example.com/walimurid
```

### Headers (Required)
```
Authorization: Bearer <JWT_TOKEN>
Content-Type: application/json
Accept-Encoding: gzip, deflate
```

---

## 1️⃣ AUTHENTICATION

### Login
```http
POST /walimurid/login
Content-Type: application/x-www-form-urlencoded

email=parent@example.com&password=password123
```

**Response (Success):**
```json
{
    "status": true,
    "message": "Login Berhasil",
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "data": {
        "user": {
            "id": 1,
            "email": "parent@example.com",
            "role_id": 5,
            "status_aktif": 1
        },
        "murid": {
            "id": 123,
            "nama": "Budi Santoso",
            "kelas": "A1",
            "tanggal_lahir": "2019-05-15"
        }
    }
}
```

**Performance:** ~80ms ✅
**Valid for:** 7 days

**React Native Implementation:**
```javascript
const login = async (email, password) => {
    try {
        const response = await fetch('https://api.example.com/walimurid/login', {
            method: 'POST',
            body: new URLSearchParams({
                email,
                password
            })
        });
        
        const data = await response.json();
        
        if (data.status) {
            // Store token securely
            await SecureStore.setItemAsync('jwt_token', data.token);
            return data;
        }
    } catch (error) {
        console.error('Login failed:', error);
    }
};
```

**Flutter Implementation:**
```dart
Future<LoginResponse> login(String email, String password) async {
    final response = await http.post(
        Uri.parse('https://api.example.com/walimurid/login'),
        body: {
            'email': email,
            'password': password,
        },
    );
    
    if (response.statusCode == 200) {
        final jsonResponse = jsonDecode(response.body);
        // Store token securely using flutter_secure_storage
        await _secureStorage.write(key: 'jwt_token', value: jsonResponse['token']);
        return LoginResponse.fromJson(jsonResponse);
    }
    throw Exception('Failed to login');
}
```

---

## 2️⃣ DASHBOARD

### Get Dashboard Data
```http
GET /walimurid/dashboard?id_anak=123
Authorization: Bearer <JWT_TOKEN>
```

**Response:**
```json
{
    "status": true,
    "data": {
        "murid": {
            "id": 123,
            "nama": "Budi Santoso",
            "kelas": "A1"
        },
        "tagihan_pending": 1500000,
        "latest_records": {
            "pencatatan": [
                {
                    "id": 1,
                    "kegiatan": "Bermain kotak geometri",
                    "tanggal": "2026-04-10"
                }
            ],
            "posting": [
                {
                    "id": 1,
                    "judul": "Aktivitas Senveri",
                    "deskripsi": "...",
                    "tanggal": "2026-04-10",
                    "content": [
                        {
                            "file_url": "https://uploads.example.com/image1.jpg"
                        }
                    ]
                }
            ]
        }
    }
}
```

**Performance:** ~150ms ✅

---

## 3️⃣ POSTING (GALLERY/ACTIVITIES)

### Get Posts with Pagination

```http
GET /walimurid/posting?id_anak=123&page=1
Authorization: Bearer <JWT_TOKEN>
```

**Query Parameters:**
- `id_anak` (required) - Student ID
- `page` (optional) - Page number, default: 1

**Response:**
```json
{
    "status": true,
    "pagination": {
        "current_page": 1,
        "per_page": 3,
        "total_pages": 5,
        "total_data": 15
    },
    "data": [
        {
            "id": 1,
            "id_anak": 123,
            "judul": "Aktivitas Senveri",
            "deskripsi": "Anak-anak melakukan aktivitas dengan kotak geometri...",
            "tanggal": "2026-04-10",
            "created_at": "2026-04-10 10:30:00",
            "content": [
                {
                    "tpost_id": 1,
                    "file_url": "https://uploads.example.com/post1_img1.jpg"
                },
                {
                    "tpost_id": 1,
                    "file_url": "https://uploads.example.com/post1_img2.jpg"
                }
            ]
        },
        // ... 2 more posts
    ]
}
```

**Performance:** ~100ms per page ✅
**Response Size:** ~40-50KB ✅

**React Native Implementation:**
```javascript
const [posts, setPosts] = useState([]);
const [pagination, setPagination] = useState({ page: 1, total_pages: 0 });
const [loading, setLoading] = useState(false);

const fetchPosts = async (page = 1) => {
    setLoading(true);
    try {
        const token = await SecureStore.getItemAsync('jwt_token');
        
        const response = await fetch(
            `https://api.example.com/walimurid/posting?id_anak=123&page=${page}`,
            {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept-Encoding': 'gzip, deflate'
                }
            }
        );
        
        const data = await response.json();
        
        if (page === 1) {
            setPosts(data.data);
        } else {
            setPosts(prev => [...prev, ...data.data]);
        }
        
        setPagination({
            page: data.pagination.current_page,
            total_pages: data.pagination.total_pages
        });
    } catch (error) {
        console.error('Error fetching posts:', error);
    } finally {
        setLoading(false);
    }
};

const handleLoadMore = () => {
    if (pagination.page < pagination.total_pages) {
        fetchPosts(pagination.page + 1);
    }
};

// In render:
// <FlatList
//     data={posts}
//     onEndReached={handleLoadMore}
//     onEndReachedThreshold={0.5}
//     renderItem={({ item }) => <PostCard {...item} />}
// />
```

**Flutter Implementation:**
```dart
class PostProvider extends ChangeNotifier {
    List<Post> posts = [];
    int currentPage = 1;
    int totalPages = 1;
    bool isLoading = false;

    Future<void> fetchPosts(int studentId, [int page = 1]) async {
        isLoading = true;
        notifyListeners();

        try {
            final token = await _secureStorage.read(key: 'jwt_token');
            
            final response = await http.get(
                Uri.parse('https://api.example.com/walimurid/posting')
                    .replace(queryParameters: {
                        'id_anak': studentId.toString(),
                        'page': page.toString(),
                    }),
                headers: {
                    'Authorization': 'Bearer $token',
                    'Accept-Encoding': 'gzip, deflate',
                },
            );

            if (response.statusCode == 200) {
                final jsonResponse = jsonDecode(response.body);
                
                if (page == 1) {
                    posts = (jsonResponse['data'] as List)
                        .map((p) => Post.fromJson(p))
                        .toList();
                } else {
                    posts.addAll(
                        (jsonResponse['data'] as List)
                            .map((p) => Post.fromJson(p))
                    );
                }
                
                currentPage = jsonResponse['pagination']['current_page'];
                totalPages = jsonResponse['pagination']['total_pages'];
            }
        } catch (e) {
            print('Error fetching posts: $e');
        } finally {
            isLoading = false;
            notifyListeners();
        }
    }

    Future<void> loadMore(int studentId) async {
        if (currentPage < totalPages) {
            await fetchPosts(studentId, currentPage + 1);
        }
    }
}
```

---

## 4️⃣ RECORDS (PENCATATAN PERKEMBANGAN)

### Get Records with Pagination

```http
GET /walimurid/pencatatan?id_anak=123&page=1&limit=10
Authorization: Bearer <JWT_TOKEN>
```

**Query Parameters:**
- `id_anak` (required) - Student ID
- `page` (optional) - Page number, default: 1
- `limit` (optional) - Items per page, max: 50, default: 10

**Response:**
```json
{
    "status": true,
    "pagination": {
        "current_page": 1,
        "per_page": 10,
        "total_pages": 5,
        "total_data": 45
    },
    "data": [
        {
            "id": 1,
            "id_anak": 123,
            "kegiatan": "Bermain kotak geometri",
            "deskripsi": "Anak mempelajari bentuk dengan kotak geometri...",
            "tanggal": "2026-04-10",
            "created_at": "2026-04-10 09:15:00"
        }
    ]
}
```

**Performance:** ~50-70ms per page ✅

---

## 5️⃣ INVOICES (TAGIHAN PEMBAYARAN)

### Get Invoices with Pagination

```http
GET /walimurid/tagihan?id_anak=123&page=1&limit=15
Authorization: Bearer <JWT_TOKEN>
```

**Response:**
```json
{
    "status": true,
    "pagination": {
        "current_page": 1,
        "per_page": 15,
        "total_pages": 2,
        "total_data": 28
    },
    "data": [
        {
            "id": 1,
            "id_anak": 123,
            "nominal": 2500000,
            "tanggal": "2026-04-01",
            "status": "Pending",
            "deskripsi": "SPP April 2026"
        }
    ]
}
```

**Performance:** ~70ms ✅

**Filtering Tips:**
- Filter by status on client side for instant UI response
- Backend will cache queries for 10 minutes
- Nominal is in Rupiah currency

---

## 6️⃣ TESTIMONIALS

### Get Testimonials with Pagination

```http
GET /walimurid/testimoni?page=1&limit=10
Authorization: Bearer <JWT_TOKEN>
```

**Response:**
```json
{
    "status": true,
    "pagination": {
        "current_page": 1,
        "per_page": 10,
        "total_pages": 3,
        "total_data": 28
    },
    "data": [
        {
            "id": 1,
            "name": "Ibu Dewi Hardjono",
            "profesi": "Product Manager",
            "description": "Sangat puas dengan perhatian guru...",
            "img": "photo1.jpg",
            "created_at": "2026-03-15"
        }
    ]
}
```

**Performance:** ~50-80ms ✅

---

## 7️⃣ SEARCH

### Global Search (Posts & Records)

```http
GET /walimurid/search?q=geometri&id_anak=123
Authorization: Bearer <JWT_TOKEN>
```

**Response:**
```json
{
    "status": true,
    "data": {
        "pencatatan": [
            {
                "id": 1,
                "kegiatan": "Bermain kotak geometri",
                "tanggal": "2026-04-10"
            }
        ],
        "posting": [
            {
                "id": 5,
                "judul": "Aktivitas Geometri Hari Ini",
                "tanggal": "2026-04-10"
            }
        ]
    }
}
```

---

## 🔐 ERROR HANDLING

### 400 Bad Request
```json
{
    "status": false,
    "message": "id_anak diperlukan"
}
```

### 401 Unauthorized
```json
{
    "status": false,
    "message": "Akses ditolak, token otorisasi Bearer tidak ditemukan"
}
```

**React Native Handling:**
```javascript
const apiCall = async (url, options = {}) => {
    try {
        const response = await fetch(url, {
            ...options,
            headers: {
                ...options.headers,
                'Authorization': `Bearer ${token}`,
            }
        });
        
        if (response.status === 401) {
            // Token expired, refresh or re-login
            await handleTokenRefresh();
            throw new Error('Token expired');
        }
        
        if (response.status === 400) {
            const error = await response.json();
            throw new Error(error.message);
        }
        
        return await response.json();
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
};
```

---

## 🎯 BEST PRACTICES FOR MOBILE

### 1. Token Management
```javascript
// Store token securely (NOT in AsyncStorage for iOS)
import * as SecureStore from 'expo-secure-store';

// Save
await SecureStore.setItemAsync('jwt_token', token);

// Retrieve  
const token = await SecureStore.getItemAsync('jwt_token');

// Delete (logout)
await SecureStore.deleteItemAsync('jwt_token');
```

### 2. Connection Handling
```javascript
// Check network before making requests
import NetInfo from '@react-native-community/netinfo';

const state = await NetInfo.fetch();
if (state.isConnected) {
    // Make API call
} else {
    // Show offline message
}
```

### 3. Image Caching
```javascript
// For image URLs in posting.content
import FastImage from 'react-native-fast-image';

<FastImage
    source={{ uri: imageUrl, cache: 'immutable' }}
    style={{ width: '100%', height: 200 }}
    resizeMode="cover"
/>
```

### 4. Pagination in FlatList
```javascript
<FlatList
    data={posts}
    keyExtractor={item => item.id.toString()}
    renderItem={({ item }) => <PostCard {...item} />}
    onEndReached={() => {
        if (pagination.page < pagination.total_pages) {
            fetchPosts(pagination.page + 1);
        }
    }}
    onEndReachedThreshold={0.5}
    ListFooterComponent={loading ? <ActivityIndicator /> : null}
/>
```

### 5. Error Retry Logic
```javascript
const retryFetch = async (url, maxRetries = 3) => {
    for (let i = 0; i < maxRetries; i++) {
        try {
            return await fetch(url);
        } catch (error) {
            if (i < maxRetries - 1) {
                // Exponential backoff
                await new Promise(resolve => 
                    setTimeout(resolve, Math.pow(2, i) * 1000)
                );
            }
        }
    }
};
```

---

## 📊 PERFORMANCE EXPECTATIONS

**All endpoints optimized for mobile:**

| Metric | Expectation | Status |
|--------|-------------|--------|
| Login | <100ms | ✅ |
| Dashboard | <200ms | ✅ |
| Posting (page) | <150ms | ✅ |
| Records (page) | <100ms | ✅ |
| Invoices (page) | <100ms | ✅ |
| Response Compression | 70% reduction | ✅ |
| Mobile 4G | All <500ms | ✅ |

---

## 🔗 API Documentation

Full API documentation available at:
```
https://littlehome-api.example.com/docs/
```

Postman Collection:
```
File: public/docs/Little_Home_API_Postman.json
```

---

## 📞 SUPPORT & CONTACT

For integration issues:
- Check [`PERFORMANCE_AUDIT_REPORT.md`](../PERFORMANCE_AUDIT_REPORT.md)
- Review [`QUICK_START.md`](../QUICK_START.md)
- Run performance check: `php bin/check-performance.php`

