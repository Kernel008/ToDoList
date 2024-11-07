<?php

namespace App\Controller;

use App\Entity\Provider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WorkListController extends AbstractController
{
    #[Route('/workList', name: 'work_list')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        // Veritabanından tüm verileri çek
        $works = $entityManager->getRepository(Provider::class)->findAll();
		
		
		// Alınan verileri formatla
		$tasks = [];

		foreach ($works as $work) {
			$tasks[] = [
				'id' => $work->getJobId(),
				'duration' => $work->getDuration(),
				'difficulty' => $work->getDifficulty(),
				'provider' => (string)$work->getProviderId(),
			];
		}


		// Developer'lar (Verimlilik ve Alınan Görevlerin Toplam Saati ve Alınan Görevler)
		$developers = [
			['name' => 'DEV1', 'efficiency' => 1, 'hours' => 0, 'tasks' => []],
			['name' => 'DEV2', 'efficiency' => 2, 'hours' => 0, 'tasks' => []],
			['name' => 'DEV3', 'efficiency' => 3, 'hours' => 0, 'tasks' => []],
			['name' => 'DEV4', 'efficiency' => 4, 'hours' => 0, 'tasks' => []],
			['name' => 'DEV5', 'efficiency' => 5, 'hours' => 0, 'tasks' => []]
		];


		// Görevleri zorluklarına göre sıralama (Azalan zorluk sırasına göre)
		usort($tasks, function ($a, $b) {
			$aValue = $a['duration'] * $a['difficulty'];
			$bValue = $b['duration'] * $b['difficulty'];
			return $bValue <=> $aValue;
		});


		// Görevleri atama
		foreach ($tasks as $task) {
			// Her görevi uygun developera atanacak şekilde sırala
			usort($developers, function($a, $b) use ($task) {
				$aLoad = $a['hours'] + ($task['duration'] * $task['difficulty'] / $a['efficiency']);
				$bLoad = $b['hours'] + ($task['duration'] * $task['difficulty'] / $b['efficiency']);
				return $aLoad <=> $bLoad;
			});

			// Görevi en az iş yüküne sahip(sıralamadan sonra ilk) developera ata
			$developers[0]['hours'] += $task['duration'] * $task['difficulty'] / $developers[0]['efficiency'];
			$developers[0]['tasks'][] = [
				'provider' => $task['provider'],
				'job' => $task['id'],
				'hour' => round($task['duration'] * $task['difficulty'] / $developers[0]['efficiency'], 2)
			];
		}
		
		
		// Developer'ları sırala (Ad'a göre)
		usort($developers, function($a, $b) {
			return $a['name'] <=> $b['name'];
		});

        // Twig şablonuna yönlendir
        return $this->render('work_list.html.twig', [
            'work_list' => $developers
        ]);
    }
}
