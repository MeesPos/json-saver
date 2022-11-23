<?php

namespace App\Console\Commands;

use App\Models\Creditcard;
use App\Models\Person;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;

class ImportJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:json';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import JSON to the database.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (file_exists(resource_path('challenge.json'))) {
            $persons = json_decode(file_get_contents(resource_path('challenge.json')));

            foreach ($persons as $person) {
                $dateOfBirth = date('Y-m-d', strtotime($person->date_of_birth));
                $age = Carbon::parse($dateOfBirth)->age;

                if (($age >= 18 && $age <= 65) || is_null($age)) {
                    // Met "UpdateOrCreate" zorgen we ervoor dat er geen duplicate records in de database komt.
                    $personModel = Person::query()
                        ->updateOrCreate([
                            'name' => $person->name,
                            'address' => $person->address,
                            'checked' => $person->checked,
                            'description' => $person->description,
                            'interest' => $person->interest,
                            'date_of_birth' => $dateOfBirth,
                            'email' => $person->email,
                            'account' => $person->account
                        ]);

                    // Met "UpdateOrCreate" zorgen we ervoor dat er geen duplicate records in de database komt.
                    Creditcard::query()
                        ->updateOrCreate([
                            'person_id' => $personModel->getKey(),
                            'type' => $person->credit_card->type,
                            'number' => $person->credit_card->number,
                            'name' => $person->credit_card->name,
                            'expiration_date' => $person->credit_card->expirationDate
                        ]);

                    $this->info($person->name . ' imported');
                }
            }

            return Command::SUCCESS;
        }

        return Command::INVALID;
    }
}
