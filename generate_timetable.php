<?php
// Include the database connection file
include("db.php");

// --- 1. Data Fetching ---
// Fetch all necessary data from the database.
$sql_subjects = "SELECT id, subjectName, hoursPerWeek FROM subjects";
$subjects_result = $conn->query($sql_subjects);
$subjects = [];
while ($row = $subjects_result->fetch_assoc()) {
    $subjects[] = $row;
}

$sql_faculties = "SELECT id, fullName, subjectHandling FROM staff WHERE role='staff'";
$faculties_result = $conn->query($sql_faculties);
$faculties = [];
while ($row = $faculties_result->fetch_assoc()) {
    $faculties[] = $row;
}

$sql_rooms = "SELECT id, roomName FROM rooms";
$rooms_result = $conn->query($sql_rooms);
$rooms = [];
while ($row = $rooms_result->fetch_assoc()) {
    $rooms[] = $row;
}

$sql_batches = "SELECT id, batchName FROM batches";
$batches_result = $conn->query($sql_batches);
$batches = [];
while ($row = $batches_result->fetch_assoc()) {
    $batches[] = $row;
}

// Map faculty to subject
$faculty_subject_map = [];
foreach ($faculties as $faculty) {
    $faculty_subject_map[$faculty['subjectHandling']][] = $faculty['id'];
}

// --- 2. Genetic Algorithm Core Functions ---

/**
 * Calculates the fitness of a single timetable (chromosome).
 * Lower is better, with 0 being a perfect timetable.
 * @param array $timetable The timetable (an array of class genes).
 * @return int The number of violations.
 */
function calculateFitness($timetable) {
    $violations = 0;
    
    // Arrays to track occupied slots for hard constraints
    $occupied_slots = [
        'faculty' => [],
        'room' => [],
        'batch' => []
    ];

    foreach ($timetable as $class_gene) {
        $faculty_id = $class_gene['faculty_id'];
        $room_id = $class_gene['room_id'];
        $batch_id = $class_gene['batch_id'];
        $time_slot = $class_gene['time_slot']; // e.g., 'Mon-9am'

        // Rule 1: One faculty, one time slot
        if (isset($occupied_slots['faculty'][$faculty_id][$time_slot])) {
            $violations++;
        }
        $occupied_slots['faculty'][$faculty_id][$time_slot] = true;

        // Rule 2: One room, one time slot
        if (isset($occupied_slots['room'][$room_id][$time_slot])) {
            $violations++;
        }
        $occupied_slots['room'][$room_id][$time_slot] = true;

        // Rule 3: One batch, one time slot
        if (isset($occupied_slots['batch'][$batch_id][$time_slot])) {
            $violations++;
        }
        $occupied_slots['batch'][$batch_id][$time_slot] = true;
    }

    return $violations;
}

/**
 * Generates a random initial timetable.
 * @return array A new, randomly generated timetable.
 */
function generateRandomTimetable($subjects, $faculties, $rooms, $batches, $faculty_subject_map) {
    $timetable = [];
    $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
    $slots_per_day = 8; // Assuming 8 slots from 9 AM to 5 PM
    $time_slots = [];
    for ($i = 9; $i <= 16; $i++) {
        foreach ($days as $day) {
            $time_slots[] = $day . '-' . $i . 'am'; // simplified time slots
        }
    }

    foreach ($subjects as $subject) {
        for ($i = 0; $i < $subject['hoursPerWeek']; $i++) {
            $assigned_faculty = null;
            if (isset($faculty_subject_map[$subject['subjectName']])) {
                $assigned_faculty = $faculty_subject_map[$subject['subjectName']][array_rand($faculty_subject_map[$subject['subjectName']])];
            }
            
            // Randomly select other components
            $assigned_room = $rooms[array_rand($rooms)]['id'];
            $assigned_batch = $batches[array_rand($batches)]['id'];
            $assigned_time_slot = $time_slots[array_rand($time_slots)];

            $timetable[] = [
                'subject_id' => $subject['id'],
                'faculty_id' => $assigned_faculty,
                'batch_id' => $assigned_batch,
                'room_id' => $assigned_room,
                'time_slot' => $assigned_time_slot
            ];
        }
    }
    return $timetable;
}

/**
 * Genetic Algorithm - Crossover function.
 * Combines two parent timetables to create an offspring.
 */
function crossover($parent1, $parent2) {
    $cut_point = rand(1, count($parent1) - 1);
    $offspring = array_merge(array_slice($parent1, 0, $cut_point), array_slice($parent2, $cut_point));
    return $offspring;
}

/**
 * Genetic Algorithm - Mutation function.
 * Randomly changes a single class assignment in the timetable.
 */
function mutate(&$timetable, $rooms, $batches, $time_slots, $faculty_subject_map) {
    $gene_to_mutate_index = array_rand($timetable);
    $gene_to_mutate = $timetable[$gene_to_mutate_index];
    
    // Mutate either the room, batch, or time slot
    $mutation_type = rand(0, 2);
    switch ($mutation_type) {
        case 0: // Mutate room
            $gene_to_mutate['room_id'] = $rooms[array_rand($rooms)]['id'];
            break;
        case 1: // Mutate batch
            $gene_to_mutate['batch_id'] = $batches[array_rand($batches)]['id'];
            break;
        case 2: // Mutate time slot
            $gene_to_mutate['time_slot'] = $time_slots[array_rand($time_slots)];
            break;
    }
    $timetable[$gene_to_mutate_index] = $gene_to_mutate;
}

// --- 3. Main Genetic Algorithm Loop ---
$population_size = 50;
$mutation_rate = 0.1;
$max_generations = 1000;

$population = [];
for ($i = 0; $i < $population_size; $i++) {
    $population[] = generateRandomTimetable($subjects, $faculties, $rooms, $batches, $faculty_subject_map);
}

for ($generation = 0; $generation < $max_generations; $generation++) {
    // Evaluate fitness for each timetable in the population
    $fitness_scores = array_map('calculateFitness', $population);

    // Find the best timetable in the current generation
    $best_score = min($fitness_scores);
    $best_timetable = $population[array_keys($fitness_scores, $best_score)[0]];

    if ($best_score === 0) {
        echo "✅ Perfect timetable found in generation " . $generation . "!\n";
        // Here, you would save this best_timetable to your database.
        // For example: saveTimetableToDB($best_timetable, $conn);
        break;
    }

    echo "Generation " . $generation . " - Best Score: " . $best_score . "\n";

    // Create a new population for the next generation
    $new_population = [$best_timetable]; // Elitism: keep the best one
    for ($i = 1; $i < $population_size; $i++) {
        // Selection: simple tournament selection
        $parent1 = $population[array_rand($population)];
        $parent2 = $population[array_rand($population)];
        $parent1 = calculateFitness($parent1) < calculateFitness($parent2) ? $parent1 : $parent2;

        $parent3 = $population[array_rand($population)];
        $parent4 = $population[array_rand($population)];
        $parent2 = calculateFitness($parent3) < calculateFitness($parent4) ? $parent3 : $parent4;
        
        // Crossover
        $offspring = crossover($parent1, $parent2);
        
        // Mutation
        if (mt_rand() / mt_getrandmax() < $mutation_rate) {
            mutate($offspring, $rooms, $batches, $time_slots, $faculty_subject_map);
        }

        $new_population[] = $offspring;
    }

    $population = $new_population;
}
?>