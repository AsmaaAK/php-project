<?php $title = $title ?? 'قائمة المستخدمين'; ?>
<h2 style="text-align:center; margin-bottom:20px; color:#007bff;"><?= htmlspecialchars($title) ?></h2>

<table style="width:100%; border-collapse:collapse; text-align:left;">
    <thead>
        <tr style="background:#007bff; color:white;">
            <th style="padding:10px; border:1px solid #ddd;">#</th>
            <th style="padding:10px; border:1px solid #ddd;">الاسم</th>
            <th style="padding:10px; border:1px solid #ddd;">البريد</th>
        </tr>
    </thead>
    <tbody>
        <?php if(!empty($users)): ?>
            <?php foreach($users as $u): ?>
                <tr>
                    <td style="padding:8px; border:1px solid #ddd;"><?= (int)$u['id'] ?></td>
                    <td style="padding:8px; border:1px solid #ddd;"><?= htmlspecialchars($u['name']) ?></td>
                    <td style="padding:8px; border:1px solid #ddd;"><?= htmlspecialchars($u['email']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3" style="text-align:center; padding:10px; border:1px solid #ddd; color:red;">
                    🚫 لا يوجد مستخدمين
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
