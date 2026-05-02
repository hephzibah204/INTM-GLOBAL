<?php
require_once __DIR__ . '/db.php';

// Seed initial content if site_content is empty
$count = $db->query("SELECT COUNT(*) FROM site_content")->fetchColumn();

if ($count == 0) {
    $defaults = [
        ['home', 'home_hero_title', 'Nourish Knowledge.<br><em>Transform Lives.</em>', 'html'],
        ['home', 'home_hero_sub', 'The Institute of Nutritional Therapy Management (INTM) is a leading centre for evidence-based nutritional education, professional development, and transformative research across Africa and beyond.', 'text'],
        ['about', 'about_intro', 'The Institute of Nutritional Therapy Management (INTM Global) is a premier professional body dedicated to advancing the field of nutritional science.', 'text'],
        ['contact', 'contact_email', 'info@intmglobal.org', 'text'],
        ['contact', 'contact_phone', '08036210735', 'text'],
        ['contact', 'contact_address', 'Plot 6, Block B, Akinyode Family Layout, Unity Estate Off Akala Expressway, Zionist Estate, New Garage, Orita Challenge, Ibadan , Oyo State', 'text']
    ];

    $stmt = $db->prepare("INSERT INTO site_content (page_id, section_id, content, type) VALUES (?, ?, ?, ?)");
    foreach ($defaults as $row) {
        $stmt->execute($row);
    }
    echo "Database seeded successfully.";
} else {
    echo "Database already contains content.";
}
?>
