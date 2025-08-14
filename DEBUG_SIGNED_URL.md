# Debug Guide untuk Signed URL Error

## Langkah-langkah Debugging

### 1. **Cek Konfigurasi S3**
Akses URL debug (hanya di environment local):
```
http://localhost/videos/debug-s3
```

Ini akan menampilkan:
- Konfigurasi S3 lengkap
- Status environment variables
- Test koneksi ke S3
- Informasi disk default

### 2. **Monitor Log Real-time**
Buka terminal dan jalankan:
```bash
# Monitor log Laravel
tail -f storage/logs/laravel.log

# Atau filter hanya untuk signed URL
tail -f storage/logs/laravel.log | grep "Signed URL"
```

### 3. **Cek Environment Variables**
Pastikan file `.env` memiliki konfigurasi S3 yang benar:
```env
FILESYSTEM_DRIVER=s3
FILESYSTEM_CLOUD=s3

AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=your_region
AWS_BUCKET=your_bucket_name
AWS_URL=https://your_bucket.s3.your_region.amazonaws.com
```

### 4. **Test Manual Signed URL**
Buka browser developer tools dan test manual:
```javascript
// Test di browser console
fetch('/videos/signed-url?filename=test.mp4&type=video/mp4')
  .then(response => response.json())
  .then(data => console.log(data))
  .catch(error => console.error('Error:', error));
```

## Log Messages yang Akan Muncul

### ✅ **Success Flow:**
```
[INFO] Signed URL request received
[INFO] Generating signed URL
[INFO] S3 Configuration check
[INFO] Signed URL generated successfully
```

### ❌ **Error Flow:**
```
[WARNING] Missing required parameters for signed URL
[WARNING] Invalid file type for signed URL
[ERROR] Error generating signed URL
```

## Common Issues & Solutions

### 1. **AWS Credentials Not Set**
**Error:** `Error generating signed URL: The AWS Access Key Id you provided does not exist in our records`

**Solution:**
- Cek `.env` file untuk AWS credentials
- Jalankan `php artisan config:clear`
- Restart server

### 2. **Wrong Region/Bucket**
**Error:** `Error generating signed URL: The specified bucket does not exist`

**Solution:**
- Pastikan bucket name benar di `.env`
- Pastikan region sesuai dengan bucket
- Cek bucket permissions

### 3. **Filesystem Driver Not S3**
**Error:** `Error generating signed URL: Call to undefined method`

**Solution:**
- Set `FILESYSTEM_DRIVER=s3` di `.env`
- Jalankan `php artisan config:clear`

### 4. **Missing AWS SDK**
**Error:** `Error generating signed URL: Class 'Aws\S3\S3Client' not found`

**Solution:**
```bash
composer require league/flysystem-aws-s3-v3
```

## Debug Commands

### Clear Cache:
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### Check Config:
```bash
php artisan tinker
>>> config('filesystems.default')
>>> config('filesystems.disks.s3')
```

### Test Storage:
```bash
php artisan tinker
>>> Storage::disk('s3')->put('test.txt', 'test content')
>>> Storage::disk('s3')->exists('test.txt')
>>> Storage::disk('s3')->delete('test.txt')
```

## Monitoring Script

Buat file `monitor_logs.sh`:
```bash
#!/bin/bash
echo "Monitoring Laravel logs for Signed URL..."
tail -f storage/logs/laravel.log | grep --line-buffered -E "(Signed URL|Error generating|S3 Configuration)"
```

Jalankan dengan:
```bash
chmod +x monitor_logs.sh
./monitor_logs.sh
```

## Expected Log Output

### Normal Request:
```json
[2024-01-15 10:30:00] local.INFO: Signed URL request received {
  "query_params": {
    "filename": "video.mp4",
    "type": "video/mp4"
  },
  "user_id": 1,
  "ip": "127.0.0.1"
}

[2024-01-15 10:30:00] local.INFO: S3 Configuration check {
  "driver": "s3",
  "bucket": "your-bucket",
  "region": "us-east-1",
  "key_exists": true,
  "secret_exists": true,
  "default_disk": "s3"
}

[2024-01-15 10:30:01] local.INFO: Signed URL generated successfully {
  "path": "videos/1642248600_video.mp4",
  "file_url": "https://your-bucket.s3.amazonaws.com/videos/1642248600_video.mp4",
  "signed_url_length": 456,
  "expires_at": "2024-01-15T11:00:00.000000Z"
}
```

## Next Steps

1. Akses `/videos/debug-s3` untuk cek konfigurasi
2. Monitor log saat mencoba upload video
3. Cek output log untuk identifikasi masalah spesifik
4. Gunakan informasi debug untuk fix konfigurasi S3
