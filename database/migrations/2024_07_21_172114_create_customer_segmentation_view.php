<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
        CREATE VIEW customer_segmentation AS
        SELECT 
            p.passenger_id,
            tr.age,
            tr.gender,
            tr.country_of_residence,
            COUNT(r.reservation_id) AS total_reservations,
            AVG(f.price) AS avg_ticket_price,
            SUM(CASE WHEN c.class_name = 'Economy' THEN 1 ELSE 0 END) AS economy_flights,
            SUM(CASE WHEN c.class_name = 'Business' THEN 1 ELSE 0 END) AS business_flights,
            COUNT(DISTINCT f.flight_id) AS total_flights
        FROM 
            passengers p
            JOIN travel_requirements tr ON p.travel_requirement_id = tr.travel_requirement_id
            LEFT JOIN reservations r ON p.passenger_id = r.passenger_id
            LEFT JOIN flights f ON r.flight_id = f.flight_id
            LEFT JOIN classes c ON f.airplane_id = c.airplane_id
        GROUP BY 
            p.passenger_id, tr.age, tr.gender, tr.country_of_residence
    ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS customer_segmentation");
    }
};
//JOIN users up ON p.user_profile_id = up.user_profile_id