import { NgModule } from '@angular/core';
import { TasksAllComponent } from './all';
import { BrowserModule } from '@angular/platform-browser';
import { LoginComponent } from './_components';
import { ROUTES } from "./login.routes";
import { AuthGuard } from './_guards';
import { AlertService, AuthenticationService } from './_services';
import { FormsModule } from '@angular/forms';
import { AlertComponent } from './_directives';

@NgModule({
  declarations: [
    LoginComponent,
    AlertComponent
  ],
  imports: [
    BrowserModule,
    FormsModule,
  ],
  providers: [
    AuthGuard,
    AuthenticationService,
    AlertService
  ]
})
export class LoginModule {
  constructor() {}
}
