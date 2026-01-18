import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AppointmentsService } from '../../services/appointments.service';
import { AuthService } from '../../services/auth.service';

@Component({
  standalone: true,
  selector: 'app-appointments',
  imports: [CommonModule, FormsModule],
  templateUrl: './appointments.component.html',
  styleUrls: ['./appointments.component.scss'],
  template: `
    <h2>Turnos</h2>

    <form (ngSubmit)="create()">
      <input type="number" [(ngModel)]="client_id" name="client_id" placeholder="Client ID" required>
      <input type="date" [(ngModel)]="date" name="date" required>
      <input type="time" [(ngModel)]="time" name="time" required>

      <button type="submit">Crear turno</button>
    </form>

    <hr>

    <ul>
      <li *ngFor="let a of appointments">
        {{ a.date }} {{ a.time }} - {{ a.client }} →
        <strong>{{ a.status }}</strong>

        <!-- SOLO ADMIN -->
        <span *ngIf="isAdmin">
          <button (click)="updateStatus(a.id, 'confirmed')">Confirmar</button>
          <button (click)="updateStatus(a.id, 'cancelled')">Cancelar</button>
        </span>
      </li>
    </ul>
  `
})
export class AppointmentsComponent implements OnInit {

  appointments: any[] = [];
  isAdmin = false;

  client_id!: number;
  date = '';
  time = '';

  constructor(
    private service: AppointmentsService,
    private auth: AuthService
  ) {}

  ngOnInit() {
    this.isAdmin = this.auth.getUserRole() === 'admin';
    this.load();
  }

  load() {
    this.service.getAppointments()
      .subscribe(data => this.appointments = data);
  }

  create() {
    this.service.createAppointment({
      client_id: this.client_id,
      date: this.date,
      time: this.time
    }).subscribe(() => this.load());
  }

  updateStatus(id: number, status: string) {
    this.service.updateStatus(id, status)
      .subscribe(() => this.load());
  }
}
