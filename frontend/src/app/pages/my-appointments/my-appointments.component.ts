import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AppointmentsService } from '../../services/appointments.service';

@Component({
  selector: 'app-my-appointments',
  standalone: true,
  imports: [CommonModule],
  template: `
    <h2>Mis turnos</h2>

    <div *ngIf="appointments.length === 0">
      No tenés turnos.
    </div>

    <ul>
      <li *ngFor="let a of appointments">
        {{ a.date }} {{ a.time }} - {{ a.status }}
      </li>
    </ul>
  `
})
export class MyAppointmentsComponent implements OnInit {

  appointments: any[] = [];

  constructor(private service: AppointmentsService) {}

  ngOnInit(): void {
  this.service.getAppointments()
    .subscribe((data: any[]) => {
      this.appointments = data;
    });
}

  
}
