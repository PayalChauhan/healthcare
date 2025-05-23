
## Setup
Build & run with Docker:
   docker-compose up --build -d

## below are the API URLs to test
#1. Register a new user
   curl -X POST http://localhost:8000/api/register
     -H "Content-Type: application/json" 
     -d '{ "name":"Alice","email":"alice@example.com","password":"secret","password_confirmation":"secret" }'
   
#2. Login
   curl -X POST http://localhost:8000/api/login
     -H "Content-Type: application/json"
     -d '{ "email":"alice@example.com","password":"secret" }'

#3. List Professionals
   curl http://localhost:8000/api/professionals
     -H "Authorization: Bearer <token>"
   
#4. Book an Appointment
   curl -X POST http://localhost:8000/api/appointments
     -H "Authorization: Bearer <token>"
     -H "Content-Type: application/json"
     -d '{ "professional_id":1, "start_time":"2025-06-01 10:00:00", "end_time":"2025-06-01 10:30:00" }'
   
#5. View User Appointments
   curl http://localhost:8000/api/appointments
     -H "Authorization: Bearer <token>"
   
#6. Cancel an Appointment
   curl -X DELETE http://localhost:8000/api/appointments/1
     -H "Authorization: Bearer <token>"
   
#7. Mark an Appointment Completed
   curl -X PATCH http://localhost:8000/api/appointments/1/complete
     -H "Authorization: Bearer <token>"
   

