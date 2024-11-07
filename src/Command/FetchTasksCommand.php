<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Provider;

#[AsCommand(
    name: 'app:fetch-tasks',
    description: 'Fetch tasks from API and save them to the database',
)]
class FetchTasksCommand extends Command
{
    private $client;
    private $entityManager;

    public function __construct(HttpClientInterface $client, EntityManagerInterface $entityManager)
    {
        $this->client = $client;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure(): void
    {
     
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
		
		//Yeni API URL ekleyebilirsiniz
        $apiUrls = [
            '1' => 'https://raw.githubusercontent.com/WEG-Technology/mock/main/mock-one', 
            '2' => 'https://raw.githubusercontent.com/WEG-Technology/mock/main/mock-two',  
        ];

		$control = true;

        foreach ($apiUrls as $provider => $url) {
            $output->writeln("Fetching data from: $provider");

            $response = $this->client->request('GET', $url);

            $data = $response->toArray();


            //Döngü ile verileri veritabanına kaydet
            foreach ($data as $task) {

                $providerEntity = new Provider();
                

			    //Veritabanında aynı provider'dan gelen aynı job_id'li bir veri eklenmiş mi kontrol et
				$existingTask = $this->entityManager->getRepository(Provider::class)
					->findOneBy(['job_id' => $task['id'], 'provider_id' => $provider]);

				//Eğer zaten bu provider'dan bu job_id'ye sahip bir veri varsa, ekleme
				if ($existingTask) {
					$output->writeln("Task ID {$task['id']} from $provider already exists.");
					continue;
				}
				
				

				//ID adının servisten her zaman id adı ile geleceği varsayıldı
				$providerEntity->setJobId($task['id']);


				//Farklı servislerden aynı id ile veriler gelebildiği için bu verileri ayrıştırmak adına apiUrls içeriğinde tanımlanan provider id değerlerini kaydediyoruz
				$providerEntity->setProviderId($provider);
				
				
				// Servislerden gelen verilerin başlıkları farklılık gösterdiği için bir kontrol eklendi.
				// Zorluk verisi farklı servislerden 'value' ve 'zorluk' isimleriyle gelirken süre verisi 'estimated_duration' ve 'sure' isimleriyle geliyor.
				// control değişkenini true yaparak key isimlerini belirtilen iki seçenek üzerinden kontrol ederek işlem yapabilirsiniz. control değişkeni false ise kontrol yapılmadan verilerin geliş sırasına göre işlem yapılacaktır.
				if(!$control){
					$taskValues = array_values($task); 
					$providerEntity->setDifficulty($taskValues[1]);
					$providerEntity->setDuration($taskValues[2]);
				}else{
					if(isset($task['value'])){
					$providerEntity->setDifficulty($task['value']);
					}else if(isset($task['zorluk'])){
						$providerEntity->setDifficulty($task['zorluk']);
					}
					
					if(isset($task['estimated_duration'])){
						$providerEntity->setDuration($task['estimated_duration']);
					}else if(isset($task['sure'])){
						$providerEntity->setDuration($task['sure']);
					}
				}

                $this->entityManager->persist($providerEntity);
            }


            $this->entityManager->flush();

            $output->writeln('Data saved to the database.');
        }

        return Command::SUCCESS;
    }
}
