# Project Naming Conventions Guide

## ✅ Pre-Commit Checklist

- [ ] Class names are PascalCase
- [ ] Variable names are camelCase
- [ ] Function names are camelCase
- [ ] File names match their classes
- [ ] Table names are snake_case and plural
- [ ] Column names are snake_case
- [ ] Routes are kebab-case
- [ ] Laravel Pint has been run
- [ ] Tests are passing

---


## 📋 Table of Contents
- [Classes](#classes)
- [Variables](#variables)
- [Functions/Methods](#functionsmethods)
- [Directories & Files](#directories--files)
- [Database](#database)
- [Routes](#routes)
- [Enums](#enums)
- [Traits](#traits)
- [Tests](#tests)

---

## Classes

### Controllers
```php
// ✅ Correct
class UserController extends Controller

// ❌ Wrong
class userController

**Rules:**
- Use **PascalCase**
- Add `Controller` suffix
- Multi-word names without separators (UserProfileController)

### Models
```php
// ✅ Correct
class User extends Model
class BlogPost extends Model

// ❌ Wrong
class Users
class blog_post
```

**Rules:**
- Use **PascalCase**
- Should be **singular**
- Table names will automatically be plural and snake_case

### Resource Classes
```php
// ✅ Correct
class UserResource extends JsonResource
class PurchaserSelectionResource extends JsonResource

// ❌ Wrong
class UserResourceAPI
class user_resource
```

### Request Classes
```php
// ✅ Correct
class StoreUserRequest extends FormRequest
class UpdateUserRequest extends FormRequest

// ❌ Wrong
class UserRequest
class userStoreRequest
```

### Service Classes
```php
// ✅ Correct
class PaymentService

// ❌ Wrong
class PaymentServices
class payment_service
```

---

## Variables

### PHP Variables
```php
// ✅ Correct
$userName = 'John';

// ❌ Wrong
$UserName = 'John';
$user_name = 'John';
$IsActive = true;
```

**Rules:**
- Use **camelCase**
- Choose descriptive and clear names
- For booleans, use prefixes like `is`, `has`, `can`, `should`

### Constants
```php
// ✅ Correct
const MAX_UPLOAD_SIZE = 1024;

// ❌ Wrong
const maxUploadSize = 1024;
const Max_Upload_Size = 1024;
```

## Functions/Methods

### Controller Methods
```php
// ✅ Correct
public function index()
public function update(Request $request, $id)

// Custom methods
public function getUserProfile($userId)

// ❌ Wrong
public function Index()
public function get_user_profile()
public function Send_Welcome_Email()
```

### Model Methods
```php
// ✅ Correct - Relationships
public function user()
public function purchaserSelections()

// ✅ Correct - Scopes
public function scopeActive($query)

// ✅ Correct - Accessors/Mutators
public function setPasswordAttribute($value)

// ❌ Wrong
public function User()
public function get_full_name()
public function SetPasswordAttribute()
```

**Rules:**
- Use **camelCase**
- Clear and descriptive names
- Verbs for actions: `create`, `update`, `delete`, `send`, `calculate`
- Relationships should match relationship type:
  - `hasOne/belongsTo`: singular
  - `hasMany/belongsToMany`: plural

---

## Directories & Files

### Directory Structure
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/           ✅ PascalCase for namespace
│   │   ├── Purchaser/       ✅
│   │   └── API/             ✅
│   ├── Requests/
│   └── Resources/
├── Models/                  ✅ Plural
├── Services/               ✅ Plural
├── Repositories/           ✅ Plural
├── Enums/                  ✅ Plural
└── Traits/                 ✅ Plural
```

### File Naming
```php
// ✅ Correct
UserController.php
BlogPostController.php

// ❌ Wrong
userController.php
User_Controller.php
purchaser-request.php
```

### View Files
```php
// ✅ Correct
resources/views/
├── admin/
│   ├── users/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   └── dashboard.blade.php
├── purchaser/
│   └── profile.blade.php
└── emails/
    └── welcome.blade.php

// ❌ Wrong
AdminUsers.blade.php
admin_users.blade.php
```

---

## Database

### Table Names
```sql
-- ✅ Correct
users
blog_posts
model_type_materials

-- ❌ Wrong
Users
BlogPost
ModelTypeMaterials
```

**Rules:**
- **snake_case**
- **Plural** form
- English words

### Column Names
```sql
-- ✅ Correct
CREATE TABLE users (
    id BIGINT PRIMARY KEY,
    first_name VARCHAR(255),
    last_name VARCHAR(255),
    email VARCHAR(255),
    phone_number VARCHAR(20),
    is_active BOOLEAN,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- ❌ Wrong
firstName
LastName
IsActive
createdAt
```

### Migration Files
```php
// ✅ Correct
2025_01_27_000000_create_users_table.php
2025_01_27_000001_add_phone_to_users_table.php
2025_01_27_000002_create_purchaser_selections_table.php

// ❌ Wrong
CreateUsersTable.php
create_Users_table.php
```

---

## Routes

### API Routes
```php
// ✅ Correct
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{user}', [UserController::class, 'show']);

// Resource routes
Route::apiResource('model-type-materials', ModelTypeMaterialController::class);

// ❌ Wrong
Route::get('/Users', ...);
Route::get('/user_profile', ...);
Route::get('/getUserProfile', ...);
```

### Route Names
```php
// ✅ Correct
Route::get('/users', [UserController::class, 'index'])->name('users.index');

// Group names  
Route::name('admin.')->prefix('admin')->group(function () {
    Route::get('/users', ...)->name('users.index'); // admin.users.index
});

// ❌ Wrong
->name('userIndex')
->name('Users.Index')
->name('user_index')
```

---

## Enums

```php
// ✅ Correct
enum PurchaserRequestStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}

// ❌ Wrong
enum PURCHASER_REQUEST_STATUS
```

**Rules:**
- Enum name: **PascalCase**
- Values: **UPPER_CASE**
- String values: **snake_case**

---

## Traits

```php
// ✅ Correct
trait HasProjectScope
trait Searchable

// ❌ Wrong
trait ProjectScope
trait searchable
trait Has_Project_Scope
```

**Rules:**
- **PascalCase**
- Start with adjective or capability: `Has`, `Can`, `Is`
- Descriptive names

---

## Tests

### Test Class Names
```php
// ✅ Correct
class UserControllerTest extends TestCase

// ❌ Wrong
class TestUserController
class User_Controller_Test
```

### Test Method Names
```php
// ✅ Correct
public function test_user_can_be_created()

// ❌ Wrong
public function testUserCanBeCreated()
public function UserCanBeCreated()
```
