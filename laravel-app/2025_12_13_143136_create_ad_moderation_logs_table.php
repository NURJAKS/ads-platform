Schema::create('ad_moderation_logs', function (Blueprint $table) {
    $table->id();

    $table->foreignId('ad_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->foreignId('admin_id')
        ->constrained('users')
        ->cascadeOnDelete();

    $table->enum('action', ['approved', 'rejected']);
    $table->text('reason')->nullable();

    $table->timestamps();
});
