from locust import HttpUser, task, between
 
class BiblioUser(HttpUser):
    wait_time = between(1, 3)
 
    @task(2)
    def homepage(self):
        self.client.get("/")
 
    @task(3)
    def list_books(self):
        self.client.get("/book/")
 
    @task(2)
    def book_detail(self):
        self.client.get("/book/3")
 
    @task(1)
    def view_reservations(self):
        self.client.get("/reservation/")
 
    @task(1)
    def login(self):
        self.client.post("/login", {
            "email": "user@biblioconnect.fr",
            "password": "password"
        })
 
    @task(1)
    def register(self):
        self.client.post("/register", {
            "email": "testuser@example.com",
            "password": "password123",
            "first_name": "Test",
            "last_name": "User",
            "address": "Paris"
        })
