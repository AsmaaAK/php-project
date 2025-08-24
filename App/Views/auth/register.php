
<h1 style="text-align:center; margin-bottom:20px; color:#007bff;">تسجيل مستخدم جديد</h1>
<form method="POST" action="/volunteer-managment/public/auth/register" style="display:grid; direction: rtl; gap:10px; max-width:400px; margin:auto;">
        <?php if (isset($_SESSION['error'])): ?>
        <div style="color:red;"> <?= $_SESSION['error'];
        unset($_SESSION['error']); ?> </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <div style="color:green;"> <?= $_SESSION['success'];
        unset($_SESSION['success']); ?> </div>
    <?php endif; ?>
    <label style=" direction: rtl;">
        الاسم
        <input type="text" name="name" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:8px;">
    </label>
    <label style=" direction: rtl;">
        البريد الإلكتروني
        <input type="email" name="email" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:8px;">
    </label>
    <label style=" direction: rtl;">
        كلمة المرور
        <input type="password" name="password" required style=" direction: rtl; width:100%; padding:8px; border:1px solid #ddd; border-radius:8px;">
    </label>
    <button type="submit" style="padding:10px; background:#007bff; color:white; border:none; border-radius:5px; cursor:pointer;">تسجيل </button>
</form>
