# 🚀 QUICK REFERENCE - Admin Management API

## Start Server
```bash
cd /Applications/XAMPP/xamppfiles/htdocs/e-rapor/sistem-pengolahan-rapor-siswa
php artisan serve --port=8081
```

**URL**: `http://localhost:8081/api/admin`

---

## 📋 Quick API Reference

### SISWA
```
GET    /siswa              List all siswa
POST   /siswa              Create siswa
GET    /siswa/{id}         Get single siswa
PUT    /siswa/{id}         Update siswa
DELETE /siswa/{id}         Delete siswa
```

**Required Fields (Create):**
```json
{
  "nis": "20240001",
  "nama_siswa": "Nama Siswa",
  "jenis_kelamin": "Laki-laki",
  "angkatan": 2024,
  "status": "Aktif"
}
```

---

### GURU
```
GET    /guru               List all guru
POST   /guru               Create guru
GET    /guru/{id}          Get single guru
PUT    /guru/{id}          Update guru
DELETE /guru/{id}          Delete guru
```

**Required Fields (Create):**
```json
{
  "nip": "12345678910",
  "nama_guru": "Nama Guru",
  "jenis_kelamin": "Laki-laki",
  "no_hp": "08123456789",
  "status": "Aktif"
}
```

---

### KELAS
```
GET    /kelas              List all kelas
POST   /kelas              Create kelas
GET    /kelas/{id}         Get single kelas
PUT    /kelas/{id}         Update kelas
DELETE /kelas/{id}         Delete kelas
```

**Required Fields (Create):**
```json
{
  "kode_kelas": "X-1",
  "nama_kelas": "Kelas X-1",
  "tingkat": 10
}
```

**Tingkat Options:** 10, 11, 12

---

### TAHUN AJARAN
```
GET    /tahun-ajaran       List all tahun ajaran
POST   /tahun-ajaran       Create tahun ajaran
GET    /tahun-ajaran/{id}  Get single tahun ajaran
PUT    /tahun-ajaran/{id}  Update tahun ajaran
DELETE /tahun-ajaran/{id}  Delete tahun ajaran
```

**Required Fields (Create):**
```json
{
  "nama": "2024/2025",
  "tanggal_mulai": "2024-07-01",
  "tanggal_selesai": "2025-06-30",
  "is_aktif": true
}
```

---

## 🔄 Quick cURL Examples

### Get All Siswa
```bash
curl http://localhost:8081/api/admin/siswa
```

### Create Siswa
```bash
curl -X POST http://localhost:8081/api/admin/siswa \
  -H "Content-Type: application/json" \
  -d '{
    "nis": "20240050",
    "nama_siswa": "New Student",
    "jenis_kelamin": "Laki-laki",
    "angkatan": 2024,
    "status": "Aktif"
  }'
```

### Update Siswa
```bash
curl -X PUT http://localhost:8081/api/admin/siswa/1 \
  -H "Content-Type: application/json" \
  -d '{
    "nama_siswa": "Updated Name"
  }'
```

### Delete Siswa
```bash
curl -X DELETE http://localhost:8081/api/admin/siswa/1
```

---

## 📱 JavaScript Fetch Template

```javascript
const API = 'http://localhost:8081/api/admin';

// GET all
fetch(`${API}/siswa`)
  .then(r => r.json())
  .then(d => console.log(d.data));

// POST create
fetch(`${API}/siswa`, {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    nis: '20240050',
    nama_siswa: 'New',
    jenis_kelamin: 'Laki-laki',
    angkatan: 2024,
    status: 'Aktif'
  })
}).then(r => r.json()).then(d => console.log(d));

// PUT update
fetch(`${API}/siswa/1`, {
  method: 'PUT',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ nama_siswa: 'Updated' })
}).then(r => r.json()).then(d => console.log(d));

// DELETE
fetch(`${API}/siswa/1`, { method: 'DELETE' })
  .then(r => r.json())
  .then(d => console.log(d));
```

---

## 💾 Using Axios

```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8081/api/admin'
});

// GET all
api.get('/siswa').then(r => console.log(r.data));

// POST
api.post('/siswa', { nis, nama_siswa, ... }).then(r => console.log(r.data));

// PUT
api.put('/siswa/1', { nama_siswa: 'Updated' }).then(r => console.log(r.data));

// DELETE
api.delete('/siswa/1').then(r => console.log(r.data));
```

---

## ✅ Response Format

**Success:**
```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... }
}
```

**Error (Validation):**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "nis": ["NIS sudah terdaftar"]
  }
}
```

---

## 🔍 Valid Values

### Jenis Kelamin
- `Laki-laki`
- `Perempuan`

### Status
- `Aktif`
- `Tidak Aktif`

### Tingkat Kelas
- `10`
- `11`
- `12`

---

## ⚠️ Common Errors

| Error | Cause | Fix |
|-------|-------|-----|
| 422 | Validation failed | Check required fields |
| 404 | Resource not found | Check ID is correct |
| 422 FK Error | Can't delete (has relations) | Delete relations first |
| Connection refused | Server not running | `php artisan serve --port=8081` |

---

## 📚 Full Documentation

- `API_DOCUMENTATION.md` - Detailed API docs
- `BACKEND_SUMMARY.md` - Backend overview
- `FRONTEND_INTEGRATION.md` - Frontend examples
- `README_BACKEND.md` - Complete guide
- `Postman_Collection.json` - Postman import

---

## 🧪 Test with Postman

1. Import `Postman_Collection.json`
2. Set `base_url` = `http://localhost:8081`
3. Run requests

---

**Last Updated:** May 24, 2026
