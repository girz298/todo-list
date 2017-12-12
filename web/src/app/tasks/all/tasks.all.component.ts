import {
  Component, OnInit,
  ViewEncapsulation
} from '@angular/core';
import { TasksService } from '../_services/tasks.service';
import { Task } from "../Task";

@Component({
  selector: 'tasks-all',
  encapsulation: ViewEncapsulation.None,
  styleUrls: [
    './tasks.all.component.scss'
  ],
  templateUrl: 'tasks.all.component.html',
  providers: [
    TasksService
  ]
})
export class TasksAllComponent implements OnInit {

  tasks: Task[] = [];

  constructor(private tasksService: TasksService) {
  }

  ngOnInit(): void {
    this.tasksService
      .getTasks()
      .then(tasks => this.tasks = tasks.map(t => {return t['data'];}));
  }
}
