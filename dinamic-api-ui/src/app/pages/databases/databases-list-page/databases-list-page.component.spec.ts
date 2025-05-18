import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DatabasesListPageComponent } from './databases-list-page.component';

describe('DatabasesListPageComponent', () => {
  let component: DatabasesListPageComponent;
  let fixture: ComponentFixture<DatabasesListPageComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [DatabasesListPageComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(DatabasesListPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
