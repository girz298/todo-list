import { Routes } from '@angular/router';
import { NoContentComponent } from './no-content';
import { LoginComponent } from './login/_components';
import { AuthGuard } from './login/_guards';

export const ROUTES: Routes = [

  { path: '', redirectTo: 'tasks', pathMatch: 'full' },

  { path: 'tasks', canActivate: [AuthGuard], loadChildren: './tasks#TasksModule' },

  { path: 'login', component: LoginComponent },

  { path: '**',    component: NoContentComponent },
];
