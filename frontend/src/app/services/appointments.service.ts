import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { AuthService } from './auth.service';

@Injectable({
  providedIn: 'root'
})
export class AppointmentsService {

  private apiUrl = 'http://localhost/Sistema/backend/appointments/index.php';

  constructor(
    private http: HttpClient,
    private auth: AuthService
  ) {}

  private getHeaders() {
  return {
    headers: new HttpHeaders({
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${this.auth.getToken()}`
    })
  };
}

  getAppointments() {
    return this.http.get<any[]>(
      `${this.apiUrl}/appointments`,
      this.getHeaders()
    );
  }

  getMyAppointments() {
    return this.http.get<any[]>(
      `${this.apiUrl}/appointments/my.php`,
      this.getHeaders()
    );
  }

  createAppointment(data: {
    client_id: number;
    date: string;
    time: string;
  }) {
    return this.http.post(
      `${this.apiUrl}/appointments`,
      data,
      this.getHeaders()
    );
  }

  updateStatus(id: number, status: string) {
    return this.http.put(
      `${this.apiUrl}/appointments?id=${id}`,
      { status },
      this.getHeaders()
    );
  }
}
