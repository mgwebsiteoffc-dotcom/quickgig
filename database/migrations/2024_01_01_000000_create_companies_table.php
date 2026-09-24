<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Avante Studio
            $table->string('slug')->unique();
            $table->string('person_name'); // Rohan Sharma
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('logo')->nullable(); // path in storage
            $table->string('initials',4);
            $table->string('plan')->default('Pro');
            $table->string('gstin')->nullable();
            $table->text('bio')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->default('Ghaziabad');
            $table->string('state')->default('Uttar Pradesh');
            $table->string('pincode')->nullable();
            $table->string('industry')->nullable();
            $table->integer('team_size')->nullable();
            $table->boolean('is_verified')->default(true);
            $table->boolean('is_active')->default(true);
            // SEO per company (for multi-tenant if needed)
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
        });
        DB::table('companies')->insert([
            ['name'=>'Avante Studio','slug'=>'avante-studio','person_name'=>'Rohan Sharma','email'=>'rohan@avante.studio','phone'=>'98765 43210','website'=>'https://avante.studio','initials'=>'AS','plan'=>'Pro','gstin'=>'09ABCDE1234F1Z5','bio'=>'Avante Studio is a content-first D2C brand lab scaling reels and ads at speed.','address'=>'Kouchery Road, Ghaziabad','city'=>'Ghaziabad','state'=>'Uttar Pradesh','pincode'=>'201001','industry'=>'D2C & Media','team_size'=>14,'is_verified'=>true,'is_active'=>true,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'BrandScale Media','slug'=>'brandscale-media','person_name'=>'Priya Kapoor','email'=>'priya@brandscale.in','phone'=>'98765 43211','website'=>'https://brandscale.in','initials'=>'BS','plan'=>'Team','gstin'=>null,'bio'=>'Performance creative agency for 50+ D2C brands.','address'=>null,'city'=>'Noida','state'=>'Uttar Pradesh','pincode'=>null,'industry'=>null,'team_size'=>null,'is_verified'=>true,'is_active'=>true,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'GrowthX Labs','slug'=>'growthx-labs','person_name'=>'Aman Verma','email'=>'aman@growthx.in','phone'=>'98765 43212','website'=>null,'initials'=>'GX','plan'=>'Starter','gstin'=>null,'bio'=>'Growth lab for early-stage startups.','address'=>null,'city'=>'Gurugram','state'=>'Haryana','pincode'=>null,'industry'=>null,'team_size'=>null,'is_verified'=>true,'is_active'=>true,'created_at'=>now(),'updated_at'=>now()],
        ]);
    }
    public function down(): void { Schema::dropIfExists('companies'); }
};
