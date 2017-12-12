class Status {
  public description;
  public handle;
}

export class Task {
  public id;
  public description;
  public status: Status;

  constructor(description) {
    this.description = description;
  }
}
