<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Volunteer;
use App\Models\Event;

class MatchingController extends Controller
{
    public function runMatch(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'غير مصرح']);
            exit;
        }

        try {
            
            $volunteers = Volunteer::all();
            $events = Event::all();

            $results = [];

            foreach ($volunteers as $volunteer) {
                foreach ($events as $event) {
                    $matchDetails = $this->calculateMatch($volunteer, $event);
                    
                    $results[] = [
                        'volunteer' => [
                            'id' => $volunteer['id'],
                            'name' => $volunteer['name'],
                            'email' => $volunteer['email'],
                            'location' => $volunteer['location'],
                            'availability' => $volunteer['availability'],
                            'skills' => json_decode($volunteer['skills'], true) 
                        ],
                        'event' => [
                            'id' => $event['id'],
                            'name' => $event['name'],
                            'location' => $event['location'],
                            'event_time' => $event['event_time'],
                            'required_Skills' => json_decode($event['required_Skills'], true) 
                        ],
                        'matchDetails' => $matchDetails
                    ];
                }
            }

            echo json_encode([
                'status' => 'success',
                'message' => 'تمت المطابقة بنجاح',
                'results' => $results
            ]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'فشل في عملية المطابقة',
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }

    private function calculateMatch(array $volunteer, array $event): array
    {
       
        $locationMatch = $volunteer['location'] === $event['location'];
        
       
        $timeMatch = $volunteer['availability'] === $event['event_time'];
        

        $volunteerSkills = json_decode($volunteer['skills'], true) ?? [];
        $eventSkills = json_decode($event['required_Skills'], true) ?? [];
        
        $matchedSkills = array_intersect(
            array_map('trim', $volunteerSkills),
            array_map('trim', $eventSkills)
        );

        $matchScore = 0;
        if ($locationMatch) $matchScore += 40;
        if ($timeMatch) $matchScore += 30;
        if (count($matchedSkills) > 0) $matchScore += 30;

        return [
            'locationMatch' => $locationMatch,
            'timeMatch' => $timeMatch,
            'matchedSkills' => array_values($matchedSkills),
            'matchScore' => $matchScore,
            'isGoodMatch' => $matchScore >= 70
        ];
    }
}