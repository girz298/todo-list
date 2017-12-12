import { NgModule } from '@angular/core';
import { RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { TasksComponent } from './tasks.component';
import { TasksAllComponent } from './all';
import { NavigationComponent } from './navigation';

@NgModule({
  declarations: [
    NavigationComponent,
    TasksComponent,
    TasksAllComponent
  ],
  imports: [
    CommonModule,
    RouterModule.forChild([
      { path: '', component: TasksComponent, children: [
          {path: 'all', component: TasksAllComponent }
      ]},
    ]),
  ]
})
export class TasksModule {
}
