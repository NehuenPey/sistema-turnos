import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class ClientsService {

  private apiUrl = 'http://localhost/Sistema/backend/clients/';

  constructor(private http: HttpClient) {}

  getClients() {
    return this.http.get<any[]>(this.apiUrl);
  }
}
