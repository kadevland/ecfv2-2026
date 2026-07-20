// MongoDB Replica Set Initialization for Cinéphoria ECF
// Initialise le replica set pour supporter les transactions

try {
    // Initialize replica set
    rs.initiate({
        _id: "rs0",
        members: [
            {
                _id: 0,
                host: "mongodb:27017",
                priority: 1
            }
        ]
    });

    print("Replica Set rs0 initialized successfully");

    // Wait for primary election
    while (!rs.isMaster().ismaster) {
        print("Waiting for primary election...");
        sleep(1000);
    }

    print("Primary node elected");

    // Create application database and user
    use('cinephoria_read');

    db.createUser({
        user: process.env.MONGODB_USERNAME || "cinephoria_mongo_user",
        pwd: process.env.MONGODB_PASSWORD,
        roles: [
            {
                role: "readWrite",
                db: "cinephoria_read"
            }
        ]
    });

    print("Application user created successfully");

    // Collections et indexes seront créés via les migrations Laravel
    // Utilise mongodb/laravel-mongodb v5.5.0 avec Schema::create()
    // Voir: database/migrations/*_create_mongodb_collections.php
    print("MongoDB read-side ready for Laravel migrations");

} catch (error) {
    print("X Error during MongoDB initialization:");
    print(error);
}
