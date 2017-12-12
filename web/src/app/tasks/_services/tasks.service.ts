import { Injectable } from '@angular/core';
import { Task } from "../Task";
import { Http } from '@angular/http';

import 'rxjs/add/operator/toPromise';

@Injectable()
export class TasksService {

  constructor( private http: Http) { }

  public getTasks(): Promise<Task[]> {
    return this.http.get('api/tasks')
      .toPromise()
      .then(response => response.json().data as Task[])
  }
}
