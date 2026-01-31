<?php
define('BASE_URL', '/raspored');

$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
if ($month < 1) $month = 1;
if ($month > 12) $month = 12;

$success = isset($_GET['success']) ? (int)$_GET['success'] : 0;
$error = isset($_GET['error']) ? $_GET['error'] : '';

function countWorkdaysMonFri(int $year, int $month): int {
    $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $count = 0;
    for ($d=1; $d <= $days; $d++) {
        $ts = strtotime(sprintf('%04d-%02d-%02d', $year, $month, $d));
        $w = (int)date('N', $ts); // 1=Mon..7=Sun
        if ($w >= 1 && $w <= 5) $count++;
    }
    return $count;
}

$workdays = countWorkdaysMonFri($year, $month);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add employee</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
  <style>
    .alert { padding: 10px 12px; border-radius: 8px; margin: 0 0 12px; font-weight: 700; }
    .ok { background: #e9f7ef; border: 1px solid #b7e2c3; }
    .bad { background: #fdecec; border: 1px solid #f5b5b5; }
    .hint { margin: 10px 0 0; font-size: 14px; color: #444; }
    .actions { margin-top: 12px; display:flex; gap:10px; justify-content:center; flex-wrap:wrap; }
    .linkbtn { text-decoration:none; padding:10px 12px; border-radius:8px; border:1px solid #d7d7d7; background:#fafafa; font-weight:700; color:#111; }
    .linkbtn:hover { background:#f0f0f0; }
    .small { font-size: 13px; color:#555; margin-top:6px; }
  </style>
</head>
<body>

<div class="container">
  <h1>Add employee</h1>

  <?php if ($success === 1): ?>
    <div class="alert ok">✅ Employee has been added.</div>
  <?php endif; ?>

  <?php if ($error !== ''): ?>
    <div class="alert bad">❌ <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <div class="small">
    Month: <b><?= str_pad((string)$month,2,'0',STR_PAD_LEFT) ?>/<?= (int)$year ?></b>
    • Working days (Mon–Fri): <b><?= $workdays ?></b>
  </div>

  <form method="post" action="<?= BASE_URL ?>/add_employee.php" class="employee-form">

    <div class="field">
      <label>First name</label>
      <input type="text" name="name" required>
    </div>

    <div class="field">
      <label>Last name</label>
      <input type="text" name="last_name" required>
    </div>

    <div class="field full">
      <label>Date of birth</label>
      <input type="date" name="birth_date" required>
    </div>

    <div class="field full">
      <label>Weekly hours</label>
      <input type="number" name="weekly_hours" step="0.5" min="1" max="60" value="40" required>
    </div>

    <label class="checkbox">
      <input type="checkbox" name="can_night" value="1">
      Available for night shifts
    </label>

    <input type="hidden" name="year" value="<?= (int)$year ?>">
    <input type="hidden" name="month" value="<?= (int)$month ?>">

    <button type="submit">➕ Add employee & generate schedule</button>
  </form>

  <div class="actions">
    <a class="linkbtn" href="<?= BASE_URL ?>/views/schedule.php?year=<?= (int)$year ?>&month=<?= (int)$month ?>">
      📅 Open schedule (<?= str_pad((string)$month,2,'0',STR_PAD_LEFT) ?>/<?= (int)$year ?>)
    </a>
  </div>
</div>

</body>
</html>
