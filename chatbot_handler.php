<?php
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message'])) {
    $query = strtolower(trim($_POST['message']));
    $response = "";

    // FEVER
    if (strpos($query, 'fever') !== false || strpos($query, 'temperature') !== false) {
        $response = "For fever, ensure plenty of rest and hydration (water/juice). You may take over-the-counter paracetamol. If fever exceeds 102°F or lasts more than 3 days, consult a doctor.";

    // DIABETES
    } elseif (strpos($query, 'diabetic') !== false || strpos($query, 'sugar') !== false) {
        $response = "Managing diabetes involves regular blood sugar monitoring, a controlled diet (low sugar, high fiber), prescribed medication, and regular exercise.";

    // ASTHMA
    } elseif (strpos($query, 'asthma') !== false) {
        $response = "For asthma, always carry your inhaler, avoid dust/smoke triggers, and follow your doctor’s prescription. If you experience severe shortness of breath, seek immediate medical attention.";

    // PILES
    } elseif (strpos($query, 'piles') !== false || strpos($query, 'hemorrhoids') !== false) {
        $response = "For piles, eat high-fiber foods (fruits, vegetables), drink plenty of water, and avoid sitting too long. Warm sitz baths can reduce discomfort. Consult a doctor if bleeding occurs.";

    // BACK PAIN
    } elseif (strpos($query, 'back pain') !== false || strpos($query, 'backache') !== false) {
        $response = "For back pain, maintain good posture, apply heat or cold packs, and perform gentle stretching. Avoid lifting heavy weights. If pain persists, consult a physiotherapist.";

    // THROAT PAIN / SORE THROAT
    } elseif (strpos($query, 'throat') !== false || strpos($query, 'sore throat') !== false) {
        $response = "For throat pain, drink warm fluids, gargle with salt water, and avoid cold foods. If accompanied by fever or lasting over 3 days, consult a doctor.";

    // HICCUPS
    } elseif (strpos($query, 'hiccup') !== false) {
        $response = "For hiccups, try holding your breath, sipping cold water, or swallowing a teaspoon of sugar. Persistent hiccups lasting over 48 hours should be medically checked.";

    // STOMACH PAIN
    } elseif (strpos($query, 'stomach pain') !== false || strpos($query, 'abdominal pain') !== false) {
        $response = "For mild stomach pain, rest and stay hydrated. Avoid spicy foods. If pain is severe or associated with vomiting/fever, seek medical advice immediately.";

    // CONSTIPATION
    } elseif (strpos($query, 'constipation') !== false) {
        $response = "For constipation, increase fiber intake, drink 2–3 liters of water daily, and stay active. Avoid processed foods. Mild laxatives may help, but consult a doctor if chronic.";

    // DIARRHEA
    } elseif (strpos($query, 'diarrhea') !== false || strpos($query, 'loose motion') !== false) {
        $response = "For diarrhea, drink ORS or electrolyte fluids, avoid street food, and rest. If symptoms persist over 2 days or include dehydration, consult a doctor.";

    // NECK PAIN
    } elseif (strpos($query, 'neck pain') !== false) {
        $response = "For neck pain, do gentle neck stretches, apply heat, and maintain proper sitting posture. If pain radiates to arms or causes numbness, see a doctor.";

    // LEG PAIN
    } elseif (strpos($query, 'leg pain') !== false) {
        $response = "For leg pain, stretch regularly, stay hydrated, and elevate your legs if swollen. Massage and warm compress may relieve muscle pain. Persistent pain may need medical evaluation.";

    // CANCER
    } elseif (strpos($query, 'cancer') !== false) {
        $response = "Cancer is a group of diseases involving abnormal cell growth. Treatment depends on type and stage — commonly surgery, chemotherapy, or radiation. Early detection through regular screening saves lives.";

    // HEADACHE / MIGRAINE
    } elseif (strpos($query, 'headache') !== false || strpos($query, 'migraine') !== false) {
        $response = "For headaches, rest in a quiet, dark room, stay hydrated, and avoid skipping meals. For migraines, avoid triggers like stress or strong lights.";

    // COMMON COLD / COUGH
    } elseif (strpos($query, 'cold') !== false || strpos($query, 'cough') !== false) {
        $response = "For common cold or cough, drink warm fluids, rest, and inhale steam. Avoid cold drinks. If symptoms last more than a week, see a healthcare provider.";

    // HEALTHY LIFESTYLE
    } elseif (strpos($query, 'healthy') !== false || strpos($query, 'tips') !== false) {
        $response = "Good health comes from a balanced diet, 30 minutes of daily exercise, adequate sleep, hydration, and stress control. Avoid smoking and limit junk food.";

    // GREETINGS
    } elseif (strpos($query, 'hello') !== false || strpos($query, 'hi') !== false) {
        $response = "Hello! I'm your basic health assistant 🤖. You can ask me about fever, asthma, back pain, diabetes, piles, and more.";

    // DEFAULT
    } else {
        $response = "I can provide general tips for common conditions like fever, asthma, diabetes, back pain, constipation, and more. Please consult a doctor for serious or emergency cases.";
    }

    echo json_encode(['reply' => $response]);
} else {
    echo json_encode(['reply' => 'Error: No message received.']);
}
?>
