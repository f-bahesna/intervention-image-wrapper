# Intervention Image Wrapper for Laravel

A **lightweight Laravel wrapper** around [Intervention Image v3](https://image.intervention.io/) that provides a fluent, clean API to **upload, resize, crop, convert, and store images** easily across multiple disks (local, S3, OSS, etc.).

It is designed sometimes to remember me about simplify image handling tasks in Laravel projects while keeping performance and code clarity.

---

## Installation

```bash
composer require f-bahesna/intervention-image-wrapper

# Publish config (optional)
php artisan vendor:publish --provider="Fbahesna\InterventionImageWrapper\ImageWrapperServiceProvider" --tag="imagewrapper-config"

```

---

## Configuration (`config/imagewrapper.php`)

```php
return [
    'disk' => env('IMAGE_WRAPPPER_DISK', 'public'),
    'quality' => env('IMAGE_WRAPPER_QUALITY', 85),
    'tmp_dir' => env('IMAGE_WRAPPER_TMP', sys_get_temp_dir()),
    'intervention' => [
        'driver' => env('IMAGE_WRAPPER_DRIVER', 'gd'), // or 'imagick'
    ],
];
```

---

## 1) Manually Upload & Resize ❌

```php
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;

$manager = new ImageManager(['driver' => 'gd']);
$image = $manager->make($request->file('avatar')->getPathname())
    ->resize(300, 300);

$filename = 'avatars/1.jpg';
Storage::disk('public')->put($filename, (string) $image->encode('jpg', 85));

$url = Storage::disk('public')->url($filename);

```

**Pain points**:

- Lots of boilerplate code (managing `$manager`, encoding, storage).
- Manual handling of `resize`, `encode`, and `disk`.

---

## 2) Upload & Resize **using `Intervention Image Wrapper`** ✅

```php
use Fbahesna\InterventionImageWrapper\Facades\ImageWrapper;

$url = ImageWrapper::load($request->file('avatar'))
    ->resize(300, 300)
    ->store('avatars/1.jpg');
```

**Advantages**:

- **Fluent & chainable** API (`load()->resize()->store()`).
- Automatically handles **storage disk** and encoding.
- Easier **unit testing** with `Orchestra\Testbench`.
- Optional helper function `imagewrap()` for even cleaner syntax.
- **Consistent quality & file naming** across the app.

---

### 2. a) Using helper for even cleaner code ✅ ✅

```php
$url = imagewrap()->load($request->file('avatar'))
    ->fit(200, 200)
    ->store('avatars/photo.webp');

```

- Quick one-liner without repeating `$manager` or storage logic.

### 2. b) Using DI (dependency injection) ✅ ✅

```php
use Fbahesna\InterventionImageWrapper\Services\ImageWrapperService;

class avatarController {
    public function __construct(private ImageWrapperService $imageWrapper)
    {
    }

    public function upload(Request $request)
    {
        $url = $this->imageWrapper
            ->load($request->file('file'))
            ->resize(400, 400)
            ->store('avatars/image.jpg');

        return response()->json(['url' => $url]);
    }
}


```

## Usage Examples

### Basic upload & resize

```php
use Fbahesna\InterventionImageWrapper\Facades\ImageWrapper;

$url = ImageWrapper::load($request->file('avatar'))
    ->resize(300, 300)
    ->store('avatars/1.jpg');
```

### Resize Image

```php
$image->resize(800, 600);
```

### Crop

```php
$image->crop(300, 300, x: 50, y: 20);
```
### Rotate

```php
$image->rotate(90);
```
### Blur

```php
$image->blur(10);
```
### Brightness

```php
$image->brightness(20);
```


### Delete an image

```php
ImageWrapper::delete('images/photo.jpg');
```

---

## Advantages

1. **Fluent & intuitive API**: Chain operations like resize, fit, crop, rotate, and store.
2. **Supports multiple filesystems**: Works with `local`, `s3`, `oss`, or any Flysystem disk.
3. **Lightweight**: Minimal wrapper over Intervention Image v3 — no heavy dependencies.
4. **Reusable & testable**: Can be easily tested with PHPUnit & Testbench.
5. **Laravel integration**: Auto-discovered service provider and optional helper function.
6. **Flexible configuration**: Set default disk, quality, temporary directory, and driver in config.

---

---

## Testing

Use [Orchestra Testbench](https://github.com/orchestral/testbench) to run PHPUnit tests:

```bash
vendor/bin/phpunit
```

---

## Contribution

PRs welcome. Please follow standard Laravel package conventions:

- PSR-4 autoloading: `Fbahesna\InterventionImageWrapper\`
- Run PHPUnit tests for every feature added.
- Keep the package lightweight and simple.

---

**Summary Table:**

| Feature | Raw Intervention Image | Using ImageWrapper |
| --- | --- | --- |
| Setup Manager | ✅ manual | ✅ auto |
| Resize / Fit | ✅ manual | ✅ chainable |
| Encode & quality | ✅ manual | ✅ automatic |
| Store to disk | ✅ manual | ✅ automatic |
| Fluent API | ❌ | ✅ |
| Reusable in controllers | ❌ harder | ✅ easy |
| Unit Testing | ❌ harder | ✅ supported with Testbench |
| Helper function | ❌ | ✅ optional `imagewrap()` |

---