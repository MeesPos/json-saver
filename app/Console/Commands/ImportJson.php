<?php

namespace App\Console\Commands;

use App\Models\Creditcard;
use App\Models\Person;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

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
        $file = 'challenge.json';

        if (file_exists(resource_path($file))) {
            $persons = json_decode(file_get_contents(resource_path($file)));

            foreach ($persons as $index => $person) {
                if (Cache::get($file . '-index') < $index) {
                    $dateOfBirth = date('Y-m-d', strtotime($person->date_of_birth));
                    $age = Carbon::parse($dateOfBirth)->age;

                    if (($age >= 18 && $age <= 65) || is_null($age) ) {
                        $personModel = Person::query()
                            ->create([
                                'name' => $person->name,
                                'address' => $person->address,
                                'checked' => $person->checked,
                                'description' => $person->description,
                                'interest' => $person->interest,
                                'date_of_birth' => $dateOfBirth,
                                'email' => $person->email,
                                'account' => $person->account
                            ]);

                        Creditcard::query()
                            ->create([
                                'person_id' => $personModel->getKey(),
                                'type' => $person->credit_card->type,
                                'number' => $person->credit_card->number,
                                'name' => $person->credit_card->name,
                                'expiration_date' => $person->credit_card->expirationDate
                            ]);

                        Cache::put($file . '-index', $index);
                    }

                    $this->info($person->name . ' imported');
                }
            }

            Cache::delete($file . '-index');

            return Command::SUCCESS;
        }

        return Command::INVALID;
    }
}
