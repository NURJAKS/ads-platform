FROM golang:1.25-alpine

WORKDIR /app

COPY go.mod go.sum ./
RUN go mod tidy

COPY . .

RUN go build -o image-service

CMD ["./image-service"]
