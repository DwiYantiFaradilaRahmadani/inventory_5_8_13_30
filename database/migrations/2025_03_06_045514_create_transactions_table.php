<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Jalankan migration untuk membuat tabel.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('item_id')
                ->constrained() 
                ->onDelete('cascade'); 
            $table->foreignId('user_id') 
                ->constrained() 
                ->onDelete('cascade'); 
            $table->string('nama_barang');
            $table->date('tanggal_transaksi'); 
            $table->enum('tipe_transaksi', ['masuk', 'keluar']); 
            $table->integer('jumlah');
            $table->timestamps();
        });
    }

    /**
     * Balikkan perubahan jika migration gagal.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
